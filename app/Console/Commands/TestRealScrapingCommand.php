<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\NewsSource;
use App\Services\ContentRewriterService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class TestRealScrapingCommand extends Command
{
    protected $signature = 'news:test-real-scraping {--source=gamebrott : News source to scrape}';
    protected $description = 'Test real scraping from actual gaming websites';

    public function handle(): int
    {
        $this->info('🌐 Testing real website scraping...');

        $sourceName = $this->option('source');
        
        // Define scraping configurations for different sites
        $scrapingConfigs = [
            'gamebrott' => [
                'url' => 'https://gamebrott.com',
                'article_selector' => 'article, .post, .entry',
                'title_selector' => 'h1, h2, h3, .title, .post-title',
                'content_selector' => '.content, .post-content, .entry-content, p',
                'link_selector' => 'a[href*="gamebrott.com"]',
            ],
            'duniagames' => [
                'url' => 'https://duniagames.co.id',
                'article_selector' => '.article-item, .news-item',
                'title_selector' => '.article-title, .news-title, h2, h3',
                'content_selector' => '.article-content, .news-content',
                'link_selector' => 'a[href*="duniagames"]',
            ],
        ];

        if (!isset($scrapingConfigs[$sourceName])) {
            $this->error("❌ Unknown source: {$sourceName}");
            $this->line("Available sources: " . implode(', ', array_keys($scrapingConfigs)));
            return self::FAILURE;
        }

        $config = $scrapingConfigs[$sourceName];
        
        try {
            $this->line("🔍 Scraping from: {$config['url']}");
            
            $client = new Client([
                'timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                ]
            ]);

            $response = $client->get($config['url']);
            $html = $response->getBody()->getContents();
            
            $this->line("✅ Successfully fetched HTML content");
            
            $crawler = new Crawler($html);
            
            // Try to find articles
            $articles = $crawler->filter($config['article_selector']);
            
            if ($articles->count() === 0) {
                $this->warn("⚠️  No articles found with selector: {$config['article_selector']}");
                
                // Try alternative selectors
                $alternativeSelectors = ['article', '.post', '.news', '.content-item', 'h2', 'h3'];
                
                foreach ($alternativeSelectors as $selector) {
                    $elements = $crawler->filter($selector);
                    if ($elements->count() > 0) {
                        $this->line("📋 Found {$elements->count()} elements with selector: {$selector}");
                        
                        // Show first few elements
                        $elements->slice(0, 3)->each(function (Crawler $node, $i) {
                            $text = trim($node->text());
                            if (strlen($text) > 50) {
                                $this->line("  {$i}: " . substr($text, 0, 100) . "...");
                            }
                        });
                    }
                }
                
                return self::SUCCESS;
            }

            $this->info("📰 Found {$articles->count()} potential articles");
            
            $createdCount = 0;
            $contentRewriter = app(ContentRewriterService::class);
            
            // Process first 3 articles
            $articles->slice(0, 3)->each(function (Crawler $article, $i) use (&$createdCount, $config, $contentRewriter, $sourceName, $client) {
                try {
                    // Extract title
                    $titleNode = $article->filter($config['title_selector']);
                    $title = $titleNode->count() > 0 ? trim($titleNode->first()->text()) : "Gaming News #{$i}";
                    
                    // Extract content
                    $contentNodes = $article->filter($config['content_selector']);
                    $content = '';
                    
                    if ($contentNodes->count() > 0) {
                        $contentNodes->each(function (Crawler $node) use (&$content) {
                            $text = trim($node->text());
                            if (strlen($text) > 20) {
                                $content .= $text . "\n\n";
                            }
                        });
                    }
                    
                    if (empty($content)) {
                        $content = trim($article->text());
                    }
                    
                    // If content is too short, try to get full article
                    if (strlen($content) < 100) {
                        // Try to find article link
                        $linkNode = $article->filter('a[href]');
                        if ($linkNode->count() > 0) {
                            $articleUrl = $linkNode->first()->attr('href');
                            
                            // Make sure it's a full URL
                            if (!str_starts_with($articleUrl, 'http')) {
                                $baseUrl = parse_url($config['url'], PHP_URL_SCHEME) . '://' . parse_url($config['url'], PHP_URL_HOST);
                                $articleUrl = $baseUrl . $articleUrl;
                            }
                            
                            try {
                                $this->line("🔗 Fetching full article: {$articleUrl}");
                                $articleResponse = $client->get($articleUrl);
                                $articleHtml = $articleResponse->getBody()->getContents();
                                $articleCrawler = new Crawler($articleHtml);
                                
                                // Try to get full content
                                $fullContentSelectors = [
                                    '.post-content', '.entry-content', '.article-content', 
                                    '.content', 'main p', '.single-content p'
                                ];
                                
                                foreach ($fullContentSelectors as $selector) {
                                    $fullContentNodes = $articleCrawler->filter($selector);
                                    if ($fullContentNodes->count() > 0) {
                                        $fullContent = '';
                                        $fullContentNodes->each(function (Crawler $node) use (&$fullContent) {
                                            $text = trim($node->text());
                                            if (strlen($text) > 20) {
                                                $fullContent .= $text . "\n\n";
                                            }
                                        });
                                        
                                        if (strlen($fullContent) > 200) {
                                            $content = $fullContent;
                                            break;
                                        }
                                    }
                                }
                                
                            } catch (\Exception $e) {
                                $this->line("⚠️  Could not fetch full article: " . $e->getMessage());
                            }
                        }
                        
                        // Still skip if content is too short after trying to get full article
                        if (strlen($content) < 100) {
                            $this->line("⏭️  Skipping short content: {$title}");
                            return;
                        }
                    }
                    
                    // Skip if title is too short or generic
                    if (strlen($title) < 10 || in_array(strtolower($title), ['home', 'news', 'gaming', 'artikel'])) {
                        $this->line("⏭️  Skipping generic title: {$title}");
                        return;
                    }
                    
                    $this->line("📝 Processing: " . substr($title, 0, 60) . "...");
                    
                    // Rewrite content
                    $rewrittenContent = $contentRewriter->rewriteContent(
                        substr($content, 0, 1000), // Limit content for testing
                        $title,
                        'gaming'
                    );
                    
                    // Find or create author
                    $user = \App\Models\User::first();
                    $author = Author::firstOrCreate(
                        ['display_name' => 'Gaming News Bot'],
                        [
                            'user_id' => $user->id,
                            'bio' => 'Bot penulis konten gaming dari ' . ucfirst($sourceName),
                            'is_active' => true,
                        ]
                    );
                    
                    // Extract featured image from article or generate one
                    $featuredImage = $this->extractFeaturedImage($article, $articleUrl ?? null, $config, $client) 
                                   ?: $this->generateFeaturedImage($title);
                    
                    // Create article
                    $newArticle = Article::create([
                        'title' => $title,
                        'content' => $rewrittenContent['content'],
                        'excerpt' => $rewrittenContent['excerpt'],
                        'featured_image' => $featuredImage,
                        'author_id' => $author->id,
                        'type' => 'news',
                        'status' => 'draft',
                        'published_at' => now(),
                        'meta' => [
                            'original_source' => $sourceName,
                            'scraped_at' => now()->toISOString(),
                            'rewritten' => true,
                            'real_scraping' => true,
                        ],
                    ]);
                    
                    $this->line("✅ Created: {$newArticle->title}");
                    $createdCount++;
                    
                } catch (\Exception $e) {
                    $this->error("❌ Error processing article: " . $e->getMessage());
                }
            });
            
            $this->newLine();
            $this->info("🎉 Real scraping completed!");
            $this->line("📊 Articles created: {$createdCount}");
            
            if ($createdCount > 0) {
                $this->line("📝 Articles are created as drafts. Use 'php artisan articles:publish-drafts' to publish them.");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Scraping failed: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Generate featured image URL based on article title.
     */
    private function generateFeaturedImage(string $title): string
    {
        // Gaming-related Unsplash images
        $gamingImages = [
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=600&fit=crop', // Gaming setup
            'https://images.unsplash.com/photo-1560253023-3ec5d502959f?w=1200&h=600&fit=crop', // Esports
            'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=1200&h=600&fit=crop', // Console gaming
            'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=1200&h=600&fit=crop', // Mobile gaming
            'https://images.unsplash.com/photo-1493711662062-fa541adb3fc8?w=1200&h=600&fit=crop', // PC gaming
            'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1200&h=600&fit=crop', // Gaming controller
        ];

        // Select image based on title content
        $gameNames = ['Mobile Legends', 'PUBG', 'Valorant', 'Free Fire', 'Dota 2', 'Call of Duty', 'Honkai', 'Resident Evil'];
        $companies = ['Activision', 'Moonton', 'Riot Games', 'Capcom', 'Tencent', 'Garena'];
        
        // Mobile games
        if (stripos($title, 'Mobile Legends') !== false || stripos($title, 'Free Fire') !== false) {
            return $gamingImages[3]; // Mobile gaming image
        }
        
        // PC games
        if (stripos($title, 'Valorant') !== false || stripos($title, 'PUBG') !== false) {
            return $gamingImages[4]; // PC gaming image
        }
        
        // Console games
        if (stripos($title, 'Call of Duty') !== false || stripos($title, 'Resident Evil') !== false) {
            return $gamingImages[2]; // Console gaming image
        }
        
        // Esports related
        if (stripos($title, 'tournament') !== false || stripos($title, 'championship') !== false || stripos($title, 'esports') !== false) {
            return $gamingImages[1]; // Esports image
        }
        
        // Default gaming setup image
        return $gamingImages[0];
    }

    /**
     * Extract featured image from the original article.
     */
    private function extractFeaturedImage(Crawler $article, ?string $articleUrl, array $config, Client $client): ?string
    {
        try {
            // First, try to find image in the article preview
            $imageSelectors = [
                'img[src]',
                '.featured-image img',
                '.post-thumbnail img',
                '.article-image img',
                '.entry-image img'
            ];

            foreach ($imageSelectors as $selector) {
                $imageNodes = $article->filter($selector);
                if ($imageNodes->count() > 0) {
                    $imageSrc = $imageNodes->first()->attr('src');
                    if ($imageSrc && $this->isValidImageUrl($imageSrc)) {
                        $fullImageUrl = $this->makeAbsoluteUrl($imageSrc, $config['url']);
                        $this->line("🖼️  Found image in preview: {$fullImageUrl}");
                        return $fullImageUrl;
                    }
                }
            }

            // If no image found in preview and we have article URL, try to get from full article
            if ($articleUrl) {
                try {
                    $this->line("🔍 Looking for images in full article...");
                    $articleResponse = $client->get($articleUrl);
                    $articleHtml = $articleResponse->getBody()->getContents();
                    $articleCrawler = new Crawler($articleHtml);

                    // Try to find featured image in full article
                    $fullArticleImageSelectors = [
                        '.featured-image img[src]',
                        '.post-thumbnail img[src]',
                        '.wp-post-image[src]',
                        '.entry-content img[src]',
                        '.post-content img[src]',
                        '.article-content img[src]',
                        'meta[property="og:image"]',
                        'meta[name="twitter:image"]'
                    ];

                    foreach ($fullArticleImageSelectors as $selector) {
                        $imageNodes = $articleCrawler->filter($selector);
                        if ($imageNodes->count() > 0) {
                            $imageSrc = '';
                            
                            // Handle meta tags differently
                            if (str_contains($selector, 'meta')) {
                                $imageSrc = $imageNodes->first()->attr('content');
                            } else {
                                $imageSrc = $imageNodes->first()->attr('src');
                            }

                            if ($imageSrc && $this->isValidImageUrl($imageSrc)) {
                                $fullImageUrl = $this->makeAbsoluteUrl($imageSrc, $config['url']);
                                $this->line("🖼️  Found image in full article: {$fullImageUrl}");
                                return $fullImageUrl;
                            }
                        }
                    }

                } catch (\Exception $e) {
                    $this->line("⚠️  Could not extract image from full article: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            $this->line("⚠️  Error extracting featured image: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if the URL is a valid image URL.
     */
    private function isValidImageUrl(string $url): bool
    {
        if (empty($url) || strlen($url) < 10) {
            return false;
        }

        // Skip placeholder images and data URLs
        if (str_contains($url, 'data:image/svg') || 
            str_contains($url, 'placeholder') || 
            str_contains($url, 'lazy') ||
            str_contains($url, 'loading') ||
            str_contains($url, 'blank.') ||
            str_contains($url, 'default.') ||
            str_contains($url, '1x1.') ||
            str_contains($url, 'spacer.')) {
            return false;
        }

        // Check for common image extensions
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        if (in_array($extension, $imageExtensions)) {
            return true;
        }

        // Check for image URLs without extensions (like CDN URLs)
        if (str_contains($url, 'image') || str_contains($url, 'img') || str_contains($url, 'photo')) {
            return true;
        }

        // Check for common image hosting domains
        $imageHosts = ['imgur.com', 'cloudinary.com', 'unsplash.com', 'pexels.com', 'pixabay.com', 'gamebrott.com'];
        $host = parse_url($url, PHP_URL_HOST);
        
        foreach ($imageHosts as $imageHost) {
            if (str_contains($host, $imageHost)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert relative URL to absolute URL.
     */
    private function makeAbsoluteUrl(string $url, string $baseUrl): string
    {
        // If already absolute URL, return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        // If protocol-relative URL
        if (str_starts_with($url, '//')) {
            $protocol = parse_url($baseUrl, PHP_URL_SCHEME);
            return $protocol . ':' . $url;
        }

        // If absolute path
        if (str_starts_with($url, '/')) {
            $parsedBase = parse_url($baseUrl);
            return $parsedBase['scheme'] . '://' . $parsedBase['host'] . $url;
        }

        // If relative path
        return rtrim($baseUrl, '/') . '/' . ltrim($url, '/');
    }
}
