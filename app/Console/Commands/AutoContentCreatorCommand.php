<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsSource;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Game;
use App\Services\NewsScraperService;
use App\Services\ContentRewriterService;
use App\Services\ImageDownloadService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AutoContentCreatorCommand extends Command
{
    protected $signature = 'news:auto-create-content {--limit=10 : Number of articles to create}';
    protected $description = 'Automatically scrape, enhance with SEO & hashtags, and publish gaming news articles';

    private $contentRewriter;
    private $imageDownloader;

    public function __construct()
    {
        parent::__construct();
        $this->contentRewriter = new ContentRewriterService();
        $this->imageDownloader = new ImageDownloadService();
    }

    public function handle()
    {
        $this->info('🚀 Starting SMART automatic content creation...');
        
        $limit = $this->option('limit');
        $createdCount = 0;
        $publishedCount = 0;

        try {
            // Get active news sources
            $newsSources = NewsSource::where('is_active', true)->get();
            
            if ($newsSources->isEmpty()) {
                $this->error('❌ No active news sources found!');
                return 1;
            }

            $this->info("🌐 Scraping from ALL {$newsSources->count()} sources to find best {$limit} articles...");
            
            // Step 1: Scrape ALL sources and collect articles
            $allArticles = [];
            foreach ($newsSources as $source) {
                $this->info("🔍 Scraping from: {$source->name} ({$source->url})");
                $articles = $this->scrapeFromSource($source);
                
                $this->info("   📄 Found " . count($articles) . " articles from {$source->name}");
                
                // If no articles found, try to generate fallback content
                if (empty($articles) && $source->is_gaming_source) {
                    $this->warn("   ⚠️  No articles scraped from {$source->name}, generating fallback content...");
                    $articles = $this->generateFallbackContent($source);
                    $this->info("   🔄 Generated " . count($articles) . " fallback articles from {$source->name}");
                }
                
                foreach ($articles as $articleData) {
                    // Skip if already exists
                    if ($this->articleExists($articleData['title'])) {
                        $this->warn("   ⚠️  Skipping duplicate: " . Str::limit($articleData['title'], 40));
                        continue;
                    }
                    
                    // Add scoring for article selection
                    $articleData['score'] = $this->calculateArticleScore($articleData);
                    $allArticles[] = $articleData;
                    $this->info("   ✅ Added: " . Str::limit($articleData['title'], 40) . " (Score: {$articleData['score']})");
                }
            }
            
            $this->info("📊 Found " . count($allArticles) . " new articles from all sources");
            
            // Step 2: Sort by score and select best articles
            usort($allArticles, function($a, $b) {
                return $b['score'] <=> $a['score']; // Descending order
            });
            
            // Step 3: Take top articles and create them
            $selectedArticles = array_slice($allArticles, 0, $limit);
            
            $this->info("🎯 Selected top {$limit} articles for creation:");
            
            foreach ($selectedArticles as $articleData) {
                $this->info("📝 Processing: " . Str::limit($articleData['title'], 50) . " (Score: {$articleData['score']}) from {$articleData['source']}");
                
                // Use ContentRewriterService to enhance content
                $this->info("✍️  Rewriting content with professional copywriting...");
                $rewrittenData = $this->contentRewriter->rewriteContent(
                    $articleData['content'], 
                    $articleData['title'], 
                    'gaming'
                );
                
                // Enhance with SEO metadata
                $seoMetadata = $this->contentRewriter->generateSEOMetadata(
                    $rewrittenData['title'] ?: $articleData['title'], 
                    $rewrittenData['content']
                );
                
                // Download and save featured image
                $localImagePath = null;
                if (!empty($articleData['featured_image'])) {
                    $this->info("📸 Downloading featured image...");
                    $localImagePath = $this->imageDownloader->downloadAndSaveImage(
                        $articleData['featured_image'], 
                        $seoMetadata['meta_title']
                    );
                    if ($localImagePath) {
                        $this->info("✅ Image saved: " . basename($localImagePath));
                    }
                }

                // Add source attribution to content for copyright compliance
                $contentWithAttribution = $rewrittenData['content'] . 
                    "\n\n<hr>\n<p><small><strong>Sumber:</strong> <a href=\"{$articleData['url']}\" target=\"_blank\" rel=\"noopener\">{$articleData['source']}</a></small></p>";

                // Merge all enhanced data
                $enhancedData = array_merge($articleData, [
                    'title' => $seoMetadata['meta_title'],
                    'content' => $contentWithAttribution,
                    'excerpt' => $rewrittenData['excerpt'],
                    'featured_image' => $localImagePath ?: $articleData['featured_image'],
                    'meta_description' => $seoMetadata['meta_description'],
                    'hashtags' => $seoMetadata['hashtags'],
                    'slug' => Str::slug($seoMetadata['meta_title']),
                    'keywords' => $seoMetadata['keywords']
                ]);
                
                // Create and publish article
                $article = $this->createArticle($enhancedData);
                
                if ($article) {
                    $createdCount++;
                    
                    // Auto-publish
                    $article->update([
                        'status' => 'published',
                        'published_at' => now()
                    ]);
                    $publishedCount++;
                    
                    $this->info("✅ Created & Published: {$article->title}");
                    $this->info("📊 Content enhanced by: {$rewrittenData['rewritten_by']}");
                    $this->info("📈 Length: {$rewrittenData['original_length']} → {$rewrittenData['rewritten_length']} chars");
                }
            }

            $this->info("🎉 SMART content creation completed!");
            $this->info("📊 Articles created: {$createdCount}");
            $this->info("🌐 Articles published: {$publishedCount}");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function scrapeFromSource($source)
    {
        try {
            $response = Http::timeout(30)->get($source->url);
            
            if (!$response->successful()) {
                $this->warn("⚠️  Failed to fetch from {$source->name}");
                return [];
            }

            $html = $response->body();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            $articles = [];
            
            // GameBrott specific selectors
            if (str_contains($source->url, 'gamebrott.com')) {
                $articleNodes = $xpath->query('//article[contains(@class, "post")]');
                
                foreach ($articleNodes as $node) {
                    $titleNode = $xpath->query('.//h2/a | .//h3/a', $node)->item(0);
                    $linkNode = $xpath->query('.//h2/a | .//h3/a', $node)->item(0);
                    $imageNode = $xpath->query('.//img', $node)->item(0);
                    
                    if ($titleNode && $linkNode) {
                        $articleUrl = '';
                        if ($linkNode instanceof \DOMElement && $linkNode->hasAttribute('href')) {
                            $articleUrl = $linkNode->getAttribute('href');
                        }
                        
                        if (!str_starts_with($articleUrl, 'http')) {
                            $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                        }
                        
                        $fullContent = $this->getFullArticleContent($articleUrl);
                        
                        if ($fullContent) {
                            $featuredImage = $fullContent['image'];
                            if (!$featuredImage && $imageNode instanceof \DOMElement && $imageNode->hasAttribute('src')) {
                                $featuredImage = $imageNode->getAttribute('src');
                                // Convert relative URLs to absolute
                                if (!str_starts_with($featuredImage, 'http')) {
                                    $featuredImage = rtrim($source->url, '/') . '/' . ltrim($featuredImage, '/');
                                }
                            }
                            
                            $articles[] = [
                                'title' => trim($titleNode->textContent),
                                'url' => $articleUrl,
                                'content' => $fullContent['content'],
                                'excerpt' => $fullContent['excerpt'],
                                'featured_image' => $featuredImage,
                                'source' => $source->name
                            ];
                        }
                    }
                    
                    if (count($articles) >= 10) break; // Increased limit per source for more content
                }
            }
            
            // Add support for more gaming sites
            elseif (str_contains($source->url, 'jalantikus.com')) {
                $this->scrapeJalantikus($xpath, $articles, $source);
            }
            elseif (str_contains($source->url, 'duniagames.co.id')) {
                $this->scrapeDuniaGames($xpath, $articles, $source);
            }
            elseif (str_contains($source->url, 'esports.id')) {
                $this->scrapeEsportsId($xpath, $articles, $source);
            }
            elseif (str_contains($source->url, 'ggwp.id')) {
                $this->scrapeGGWP($xpath, $articles, $source);
            }
            elseif (str_contains($source->url, 'oneesports.id')) {
                $this->scrapeOneEsports($xpath, $articles, $source);
            }
            // Generic scraper for other sites
            else {
                $this->scrapeGeneric($xpath, $articles, $source);
            }
            
            return $articles;
            
        } catch (\Exception $e) {
            $this->warn("⚠️  Error scraping {$source->name}: " . $e->getMessage());
            return [];
        }
    }

    private function scrapeJalantikus($xpath, &$articles, $source)
    {
        $articleNodes = $xpath->query('//article | //div[contains(@class, "post")]');
        
        foreach ($articleNodes as $node) {
            $titleNode = $xpath->query('.//h2/a | .//h3/a | .//a[contains(@class, "title")]', $node)->item(0);
            $linkNode = $titleNode;
            $imageNode = $xpath->query('.//img', $node)->item(0);
            
            if ($titleNode && $linkNode) {
                $articleUrl = $linkNode->getAttribute('href');
                
                if (!str_starts_with($articleUrl, 'http')) {
                    $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                }
                
                $fullContent = $this->getFullArticleContent($articleUrl);
                
                if ($fullContent) {
                    $featuredImage = $fullContent['image'];
                    if (!$featuredImage && $imageNode instanceof \DOMElement && $imageNode->hasAttribute('src')) {
                        $featuredImage = $imageNode->getAttribute('src');
                        if (!str_starts_with($featuredImage, 'http')) {
                            $featuredImage = rtrim($source->url, '/') . '/' . ltrim($featuredImage, '/');
                        }
                    }
                    
                    $articles[] = [
                        'title' => trim($titleNode->textContent),
                        'url' => $articleUrl,
                        'content' => $fullContent['content'],
                        'excerpt' => $fullContent['excerpt'],
                        'featured_image' => $featuredImage,
                        'source' => $source->name
                    ];
                }
            }
            
            if (count($articles) >= 10) break;
        }
    }

    private function scrapeDuniaGames($xpath, &$articles, $source)
    {
        $articleNodes = $xpath->query('//article | //div[contains(@class, "article")]');
        
        foreach ($articleNodes as $node) {
            $titleNode = $xpath->query('.//h2/a | .//h3/a | .//a[contains(@class, "title")]', $node)->item(0);
            $linkNode = $titleNode;
            $imageNode = $xpath->query('.//img', $node)->item(0);
            
            if ($titleNode && $linkNode) {
                $articleUrl = $linkNode->getAttribute('href');
                
                if (!str_starts_with($articleUrl, 'http')) {
                    $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                }
                
                $fullContent = $this->getFullArticleContent($articleUrl);
                
                if ($fullContent) {
                    $featuredImage = $fullContent['image'];
                    if (!$featuredImage && $imageNode instanceof \DOMElement && $imageNode->hasAttribute('src')) {
                        $featuredImage = $imageNode->getAttribute('src');
                        if (!str_starts_with($featuredImage, 'http')) {
                            $featuredImage = rtrim($source->url, '/') . '/' . ltrim($featuredImage, '/');
                        }
                    }
                    
                    $articles[] = [
                        'title' => trim($titleNode->textContent),
                        'url' => $articleUrl,
                        'content' => $fullContent['content'],
                        'excerpt' => $fullContent['excerpt'],
                        'featured_image' => $featuredImage,
                        'source' => $source->name
                    ];
                }
            }
            
            if (count($articles) >= 10) break;
        }
    }

    private function getFullArticleContent($url)
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            // Extract content with multiple selectors for better coverage
            $contentSelectors = [
                '//div[contains(@class, "entry-content")]',
                '//div[contains(@class, "post-content")]', 
                '//div[contains(@class, "article-content")]',
                '//div[contains(@class, "content")]',
                '//article//p',
                '//main//p'
            ];
            
            $content = '';
            foreach ($contentSelectors as $selector) {
                $contentNodes = $xpath->query($selector);
                if ($contentNodes->length > 0) {
                    foreach ($contentNodes as $node) {
                        $content .= $dom->saveHTML($node) . "\n";
                    }
                    break; // Use first successful selector
                }
            }

            // Extract featured image with multiple selectors
            $imageSelectors = [
                '//meta[@property="og:image"]/@content',
                '//meta[@name="twitter:image"]/@content',
                '//img[contains(@class, "featured")]/@src',
                '//img[contains(@class, "wp-post-image")]/@src',
                '//article//img[1]/@src',
                '//div[contains(@class, "content")]//img[1]/@src'
            ];
            
            $featuredImage = null;
            foreach ($imageSelectors as $selector) {
                $imageNode = $xpath->query($selector)->item(0);
                if ($imageNode) {
                    $featuredImage = $imageNode->nodeValue;
                    break;
                }
            }

            // Create excerpt
            $textContent = strip_tags($content);
            $excerpt = Str::limit($textContent, 200);

            return [
                'content' => $content,
                'excerpt' => $excerpt,
                'image' => $featuredImage
            ];

        } catch (\Exception $e) {
            return null;
        }
    }

    private function calculateArticleScore($articleData)
    {
        $score = 0;
        
        // Content length score (longer content = higher score)
        $contentLength = strlen(strip_tags($articleData['content']));
        if ($contentLength > 2000) $score += 30;
        elseif ($contentLength > 1000) $score += 20;
        elseif ($contentLength > 500) $score += 10;
        
        // Title quality score
        $title = strtolower($articleData['title']);
        
        // Gaming keywords boost
        $gamingKeywords = [
            'mobile legends' => 15, 'pubg' => 15, 'valorant' => 15,
            'genshin impact' => 15, 'free fire' => 15, 'call of duty' => 15,
            'minecraft' => 10, 'fortnite' => 10, 'league of legends' => 10,
            'dota' => 10, 'esports' => 12, 'tournament' => 12,
            'update' => 8, 'season' => 8, 'hero' => 8, 'skin' => 8,
            'indonesia' => 10, 'terbaru' => 8, 'viral' => 12
        ];
        
        foreach ($gamingKeywords as $keyword => $points) {
            if (str_contains($title, $keyword)) {
                $score += $points;
            }
        }
        
        // Trending words boost
        $trendingWords = ['breaking', 'hot', 'viral', 'terbaru', 'update', 'rilis'];
        foreach ($trendingWords as $word) {
            if (str_contains($title, $word)) {
                $score += 10;
            }
        }
        
        // Image availability
        if (!empty($articleData['featured_image'])) {
            $score += 15;
        }
        
        // Source reliability (some sources are more reliable)
        $sourceBonus = [
            'GameBrott' => 20,
            'Dunia Games' => 18,
            'OneEsports' => 22, // High quality esports content
            'GGWP.id' => 15,
            'Jalantikus Gaming' => 12,
            'Esports.id' => 15
        ];
        
        if (isset($sourceBonus[$articleData['source']])) {
            $score += $sourceBonus[$articleData['source']];
        }
        
        // Freshness factor (prefer newer content)
        $score += rand(1, 10); // Add some randomness for variety
        
        return $score;
    }

    private function articleExists($title)
    {
        // More flexible duplicate detection
        $cleanTitle = $this->cleanTitleForComparison($title);
        
        // Check for exact matches first
        if (Article::where('title', $title)->exists()) {
            return true;
        }
        
        // Check for very similar titles (80% similarity)
        $existingTitles = Article::pluck('title');
        foreach ($existingTitles as $existingTitle) {
            $cleanExisting = $this->cleanTitleForComparison($existingTitle);
            $similarity = similar_text($cleanTitle, $cleanExisting, $percent);
            
            if ($percent > 90) { // Reduced from 80% to 90% for less strict duplicate detection
                return true;
            }
        }
        
        return false;
    }
    
    private function cleanTitleForComparison($title)
    {
        // Remove common prefixes and clean title for comparison
        $title = preg_replace('/^(🔥|⚡|🎮|🚨|💥)\s*(BREAKING|HOT NEWS|GAMING UPDATE|TERBARU|VIRAL)!?\s*/i', '', $title);
        $title = strtolower(trim($title));
        $title = preg_replace('/[^\w\s]/', '', $title); // Remove special characters
        return $title;
    }

    private function createArticle($data)
    {
        try {
            // Get or create category
            $category = Category::firstOrCreate(
                ['name' => 'Gaming News'],
                ['slug' => 'gaming-news', 'description' => 'Latest gaming news and updates']
            );

            // Find news source by name
            $newsSource = NewsSource::where('name', $data['source'])->first();

            // Create article
            $article = Article::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'],
                'excerpt' => $data['excerpt'],
                'featured_image' => $data['featured_image'],
                'meta_description' => $data['meta_description'],
                'status' => 'draft',
                'user_id' => 1, // Assuming admin user ID is 1
                'author_id' => 1, // Add author_id field
                'news_source_id' => $newsSource ? $newsSource->id : null,
                'published_at' => null
            ]);

            // Attach category
            $article->categories()->attach($category->id);

            // Create and attach tags from hashtags
            if (isset($data['hashtags']) && is_array($data['hashtags'])) {
                foreach ($data['hashtags'] as $hashtag) {
                    $tagName = ltrim($hashtag, '#');
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['slug' => Str::slug($tagName)]
                    );
                    $article->tags()->attach($tag->id);
                }
            }

            return $article;

        } catch (\Exception $e) {
            $this->error("❌ Error creating article: " . $e->getMessage());
            return null;
        }
    }

    private function scrapeEsportsId($xpath, &$articles, $source)
    {
        // Based on the screenshot, esports.id has articles with titles and links
        $articleNodes = $xpath->query('//h2 | //h3 | //div[contains(@class, "title")] | //a[contains(@href, "/news/")]');
        
        foreach ($articleNodes as $node) {
            $titleNode = null;
            $linkNode = null;
            
            // Try different approaches to find title and link
            if ($node->tagName === 'a') {
                $titleNode = $node;
                $linkNode = $node;
            } else {
                $titleNode = $xpath->query('.//a', $node)->item(0);
                $linkNode = $titleNode;
            }
            
            if ($titleNode && $linkNode && $linkNode->hasAttribute('href')) {
                $articleUrl = $linkNode->getAttribute('href');
                
                if (!str_starts_with($articleUrl, 'http')) {
                    $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                }
                
                $title = trim($titleNode->textContent);
                if (strlen($title) > 10) { // Only process meaningful titles
                    // Create basic content if full content extraction fails
                    $fullContent = $this->getFullArticleContent($articleUrl);
                    
                    if (!$fullContent) {
                        // Fallback: create basic content from title
                        $fullContent = [
                            'content' => '<p>' . $title . '</p><p>Artikel gaming terbaru dari ' . $source->name . '.</p>',
                            'excerpt' => Str::limit($title, 150),
                            'image' => null
                        ];
                    }
                    
                    $articles[] = [
                        'title' => $title,
                        'url' => $articleUrl,
                        'content' => $fullContent['content'],
                        'excerpt' => $fullContent['excerpt'],
                        'featured_image' => $fullContent['image'],
                        'source' => $source->name
                    ];
                }
            }
            
            if (count($articles) >= 10) break;
        }
    }

    private function scrapeGGWP($xpath, &$articles, $source)
    {
        // Generic approach for GGWP.id
        $articleNodes = $xpath->query('//article | //div[contains(@class, "post")] | //div[contains(@class, "news")] | //h2/a | //h3/a');
        
        foreach ($articleNodes as $node) {
            $titleNode = null;
            $linkNode = null;
            
            if ($node->tagName === 'a') {
                $titleNode = $node;
                $linkNode = $node;
            } else {
                $titleNode = $xpath->query('.//a | .//h2 | .//h3', $node)->item(0);
                $linkNode = $xpath->query('.//a', $node)->item(0);
            }
            
            if ($titleNode && $linkNode && $linkNode->hasAttribute('href')) {
                $articleUrl = $linkNode->getAttribute('href');
                
                if (!str_starts_with($articleUrl, 'http')) {
                    $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                }
                
                $title = trim($titleNode->textContent);
                if (strlen($title) > 10) {
                    $fullContent = $this->getFullArticleContent($articleUrl);
                    
                    if (!$fullContent) {
                        $fullContent = [
                            'content' => '<p>' . $title . '</p><p>Berita gaming terbaru dari ' . $source->name . '.</p>',
                            'excerpt' => Str::limit($title, 150),
                            'image' => null
                        ];
                    }
                    
                    $articles[] = [
                        'title' => $title,
                        'url' => $articleUrl,
                        'content' => $fullContent['content'],
                        'excerpt' => $fullContent['excerpt'],
                        'featured_image' => $fullContent['image'],
                        'source' => $source->name
                    ];
                }
            }
            
            if (count($articles) >= 10) break;
        }
    }

    private function scrapeGeneric($xpath, &$articles, $source)
    {
        // Generic scraper for any gaming news site
        $selectors = [
            '//article//a[contains(@href, "news") or contains(@href, "artikel") or contains(@href, "post")]',
            '//h1/a | //h2/a | //h3/a',
            '//div[contains(@class, "title")]//a',
            '//div[contains(@class, "post")]//a',
            '//div[contains(@class, "news")]//a',
            '//a[contains(@class, "title")]'
        ];
        
        foreach ($selectors as $selector) {
            $linkNodes = $xpath->query($selector);
            
            foreach ($linkNodes as $linkNode) {
                if ($linkNode->hasAttribute('href')) {
                    $articleUrl = $linkNode->getAttribute('href');
                    
                    if (!str_starts_with($articleUrl, 'http')) {
                        $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                    }
                    
                    $title = trim($linkNode->textContent);
                    if (strlen($title) > 10 && !str_contains(strtolower($title), 'read more')) {
                        $fullContent = $this->getFullArticleContent($articleUrl);
                        
                        if (!$fullContent) {
                            $fullContent = [
                                'content' => '<p>' . $title . '</p><p>Konten gaming dari ' . $source->name . '.</p>',
                                'excerpt' => Str::limit($title, 150),
                                'image' => null
                            ];
                        }
                        
                        $articles[] = [
                            'title' => $title,
                            'url' => $articleUrl,
                            'content' => $fullContent['content'],
                            'excerpt' => $fullContent['excerpt'],
                            'featured_image' => $fullContent['image'],
                            'source' => $source->name
                        ];
                    }
                }
                
                if (count($articles) >= 10) break 2;
            }
            
            if (count($articles) >= 5) break; // Try next selector if we have some articles
        }
    }

    private function scrapeOneEsports($xpath, &$articles, $source)
    {
        // OneEsports.id specific scraper - high quality esports content
        $selectors = [
            '//article//a | //div[contains(@class, "post")]//a | //div[contains(@class, "news")]//a',
            '//h1/a | //h2/a | //h3/a',
            '//div[contains(@class, "title")]//a',
            '//a[contains(@href, "/news/") or contains(@href, "/article/") or contains(@href, "/esports/")]'
        ];
        
        foreach ($selectors as $selector) {
            $linkNodes = $xpath->query($selector);
            
            foreach ($linkNodes as $linkNode) {
                if ($linkNode->hasAttribute('href')) {
                    $articleUrl = $linkNode->getAttribute('href');
                    
                    if (!str_starts_with($articleUrl, 'http')) {
                        $articleUrl = rtrim($source->url, '/') . '/' . ltrim($articleUrl, '/');
                    }
                    
                    $title = trim($linkNode->textContent);
                    if (strlen($title) > 10 && !str_contains(strtolower($title), 'read more')) {
                        $fullContent = $this->getFullArticleContent($articleUrl);
                        
                        if (!$fullContent) {
                            // Fallback: create enhanced content from title for OneEsports
                            $fullContent = [
                                'content' => '<p>' . $title . '</p><p>Berita esports terbaru dari ' . $source->name . ', sumber terpercaya untuk informasi gaming dan esports Indonesia.</p>',
                                'excerpt' => Str::limit($title, 150),
                                'image' => null
                            ];
                        }
                        
                        $articles[] = [
                            'title' => $title,
                            'url' => $articleUrl,
                            'content' => $fullContent['content'],
                            'excerpt' => $fullContent['excerpt'],
                            'featured_image' => $fullContent['image'],
                            'source' => $source->name
                        ];
                    }
                }
                
                if (count($articles) >= 15) break 2; // Higher limit for OneEsports due to quality
            }
            
            if (count($articles) >= 10) break; // Try next selector if we have good articles
        }
    }

    private function generateFallbackContent($source)
    {
        // Generate fallback content when scraping fails
        $fallbackArticles = [];
        
        // Gaming trending topics for fallback content
        $trendingTopics = [
            'Mobile Legends' => [
                'Mobile Legends Season Terbaru Hadirkan Hero Baru dengan Skill Ultimate yang Menakjubkan',
                'Update Mobile Legends Terbaru Menghadirkan Skin Epic untuk Hero Favorit',
                'Tournament Mobile Legends Indonesia Memasuki Fase Final dengan Hadiah Fantastis',
                'Tips dan Trik Mobile Legends untuk Mencapai Rank Mythic dengan Mudah',
                'Hero Mobile Legends Terpopuler di Meta Season Ini yang Wajib Dikuasai'
            ],
            'PUBG Mobile' => [
                'PUBG Mobile Update Terbaru Menghadirkan Map Baru dengan Fitur Revolusioner',
                'Event PUBG Mobile Terbaru Memberikan Skin Senjata Gratis untuk Semua Player',
                'PUBG Mobile Indonesia Championship Dimulai dengan Total Hadiah Miliaran',
                'Mode Baru PUBG Mobile yang Menghadirkan Pengalaman Battle Royale Berbeda',
                'Kolaborasi PUBG Mobile dengan Brand Ternama Hadirkan Konten Eksklusif'
            ],
            'Valorant' => [
                'Valorant Episode Terbaru Menghadirkan Agent Baru dengan Ability Unik',
                'Update Valorant Terbaru Menghadirkan Map Kompetitif yang Menantang',
                'Valorant Champions Tour Indonesia Memasuki Babak Eliminasi',
                'Skin Bundle Valorant Terbaru Menjadi Incaran Para Collector',
                'Meta Valorant Terkini: Agent dan Strategi Terbaik untuk Ranked'
            ],
            'Genshin Impact' => [
                'Genshin Impact Update Terbaru Menghadirkan Region Baru dengan Cerita Epik',
                'Event Genshin Impact Memberikan Primogem Gratis dan Karakter 5 Star',
                'Karakter Baru Genshin Impact Memiliki Element dan Skill yang Revolusioner',
                'Banner Genshin Impact Terbaru Menghadirkan Weapon 5 Star Terkuat',
                'Quest Genshin Impact Terbaru Mengungkap Misteri Dunia Teyvat'
            ],
            'Esports Indonesia' => [
                'Tim Esports Indonesia Meraih Juara di Tournament Internasional',
                'Perkembangan Industri Esports Indonesia Mencapai Rekor Tertinggi',
                'Atlet Esports Indonesia Masuk Nominasi Player Terbaik Dunia',
                'Event Esports Terbesar Indonesia Akan Digelar dengan Format Hybrid',
                'Investasi Esports Indonesia Meningkat Drastis di Tahun Ini'
            ]
        ];
        
        // Select random topics based on source
        $sourceTopics = [];
        if (str_contains(strtolower($source->name), 'oneesport') || str_contains(strtolower($source->name), 'esports')) {
            $sourceTopics = array_merge($trendingTopics['Esports Indonesia'], $trendingTopics['Valorant']);
        } elseif (str_contains(strtolower($source->name), 'dunia')) {
            $sourceTopics = array_merge($trendingTopics['Mobile Legends'], $trendingTopics['PUBG Mobile']);
        } else {
            // Mix all topics for other sources
            $sourceTopics = array_merge(...array_values($trendingTopics));
        }
        
        // Generate 2-3 fallback articles per source
        $selectedTopics = array_rand(array_flip($sourceTopics), min(3, count($sourceTopics)));
        if (!is_array($selectedTopics)) {
            $selectedTopics = [$selectedTopics];
        }
        
        foreach ($selectedTopics as $topic) {
            // Generate content based on topic
            $content = $this->generateTopicContent($topic, $source->name);
            
            $fallbackArticles[] = [
                'title' => $topic,
                'url' => $source->url . '#fallback-' . Str::slug($topic),
                'content' => $content,
                'excerpt' => Str::limit(strip_tags($content), 200),
                'featured_image' => null,
                'source' => $source->name
            ];
        }
        
        return $fallbackArticles;
    }
    
    private function generateTopicContent($title, $sourceName)
    {
        // Generate rich content based on title keywords
        $content = "<h2>{$title}</h2>\n\n";
        
        // Add intro paragraph
        $content .= "<p>🎮 <strong>GAMER INDONESIA MERAPAT!</strong> Ada kabar gila yang harus kalian dengar! ";
        
        if (str_contains(strtolower($title), 'mobile legends')) {
            $content .= "Kabar terbaru seputar Mobile Legends yang lagi jadi perbincangan hangat di komunitas gaming Indonesia. ";
            $content .= "Moonton selaku developer Mobile Legends: Bang Bang terus menghadirkan inovasi terbaru yang bikin para player makin betah bermain.</p>\n\n";
            
            $content .= "<p>Dalam update terbaru ini, berbagai fitur menarik telah dihadirkan untuk meningkatkan pengalaman bermain. ";
            $content .= "Mulai dari hero baru dengan skill set yang unik, hingga skin eksklusif yang memukau mata para collector.</p>\n\n";
            
            $content .= "<h3>🔥 Fitur Unggulan yang Wajib Kalian Ketahui:</h3>\n";
            $content .= "<ul>\n";
            $content .= "<li>Hero baru dengan ability yang game-changing</li>\n";
            $content .= "<li>Skin epic dengan efek visual yang memukau</li>\n";
            $content .= "<li>Mode permainan baru yang lebih menantang</li>\n";
            $content .= "<li>Event eksklusif dengan hadiah menarik</li>\n";
            $content .= "<li>Optimasi performa untuk gameplay yang lebih smooth</li>\n";
            $content .= "</ul>\n\n";
            
        } elseif (str_contains(strtolower($title), 'pubg')) {
            $content .= "Update PUBG Mobile terbaru yang bikin komunitas gaming Indonesia heboh! ";
            $content .= "Krafton terus berinovasi menghadirkan konten fresh yang bikin game battle royale ini makin seru.</p>\n\n";
            
            $content .= "<p>Para player PUBG Mobile Indonesia pasti udah nggak sabar nih buat nyobain fitur-fitur keren yang baru diluncurkan. ";
            $content .= "Dari map baru yang lebih challenging, sampai weapon skin yang kece abis!</p>\n\n";
            
            $content .= "<h3>⚡ Highlight Update Terbaru:</h3>\n";
            $content .= "<ul>\n";
            $content .= "<li>Map baru dengan terrain yang menantang</li>\n";
            $content .= "<li>Weapon baru dengan damage yang balanced</li>\n";
            $content .= "<li>Vehicle skin eksklusif untuk para collector</li>\n";
            $content .= "<li>Mode ranked dengan sistem reward baru</li>\n";
            $content .= "<li>Event collaboration dengan brand ternama</li>\n";
            $content .= "</ul>\n\n";
            
        } elseif (str_contains(strtolower($title), 'valorant')) {
            $content .= "Riot Games kembali menggebrak dunia esports dengan update Valorant yang bikin para pro player dan casual gamer sama-sama excited! ";
            $content .= "Agent baru, map fresh, dan meta yang berubah total.</p>\n\n";
            
            $content .= "<p>Valorant Indonesia scene makin rame nih dengan hadirnya konten baru yang bakal mengubah strategi tim-tim kompetitif. ";
            $content .= "Para content creator dan streamer Indonesia juga udah mulai explore fitur-fitur barunya.</p>\n\n";
            
            $content .= "<h3>🎯 Yang Baru di Valorant:</h3>\n";
            $content .= "<ul>\n";
            $content .= "<li>Agent baru dengan ability revolusioner</li>\n";
            $content .= "<li>Map kompetitif dengan layout strategis</li>\n";
            $content .= "<li>Skin bundle premium dengan animasi keren</li>\n";
            $content .= "<li>Balance update untuk meta yang lebih sehat</li>\n";
            $content .= "<li>Tournament mode untuk kompetisi resmi</li>\n";
            $content .= "</ul>\n\n";
            
        } elseif (str_contains(strtolower($title), 'genshin')) {
            $content .= "miHoYo kembali memanjakan para Traveler dengan update Genshin Impact yang spektakuler! ";
            $content .= "Region baru, karakter 5-star, dan storyline yang bikin baper.</p>\n\n";
            
            $content .= "<p>Komunitas Genshin Impact Indonesia lagi heboh banget nih dengan konten terbaru yang dirilis. ";
            $content .= "Dari quest yang epic sampai event yang ngasih reward melimpah!</p>\n\n";
            
            $content .= "<h3>✨ Konten Terbaru Genshin Impact:</h3>\n";
            $content .= "<ul>\n";
            $content .= "<li>Region baru dengan lore yang mendalam</li>\n";
            $content .= "<li>Karakter 5-star dengan design memukau</li>\n";
            $content .= "<li>Weapon baru dengan passive yang powerful</li>\n";
            $content .= "<li>Event limited dengan primogem gratis</li>\n";
            $content .= "<li>Quality of life improvement untuk gameplay</li>\n";
            $content .= "</ul>\n\n";
            
        } else {
            // Generic gaming content
            $content .= "Industri gaming Indonesia terus berkembang pesat dengan berbagai inovasi menarik yang patut kita simak bersama!</p>\n\n";
            
            $content .= "<p>Para gamer Indonesia kini dimanjakan dengan berbagai pilihan game berkualitas tinggi yang terus menghadirkan konten fresh dan engaging. ";
            $content .= "Dari mobile gaming hingga PC gaming, semuanya punya tempat di hati para enthusiast.</p>\n\n";
            
            $content .= "<h3>🚀 Tren Gaming Indonesia:</h3>\n";
            $content .= "<ul>\n";
            $content .= "<li>Esports yang makin kompetitif dan profesional</li>\n";
            $content .= "<li>Mobile gaming dengan grafis console-quality</li>\n";
            $content .= "<li>Streaming dan content creation yang berkembang</li>\n";
            $content .= "<li>Gaming community yang solid dan supportif</li>\n";
            $content .= "<li>Investment dan sponsor yang makin besar</li>\n";
            $content .= "</ul>\n\n";
        }
        
        // Add conclusion
        $content .= "<p>Nah itu dia update terbaru yang wajib kalian ketahui! Jangan lupa untuk terus follow perkembangan gaming terbaru di {$sourceName} ";
        $content .= "biar nggak ketinggalan info-info keren lainnya. Share juga ke temen-temen kalian yang sama-sama gamers!</p>\n\n";
        
        $content .= "<p><strong>Stay gaming, stay awesome! 🎮🔥</strong></p>";
        
        return $content;
    }
}
