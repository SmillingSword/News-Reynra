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
                $this->info("🔍 Scraping: {$source->name}");
                $articles = $this->scrapeFromSource($source);
                
                $this->info("   📄 Found " . count($articles) . " articles from {$source->name}");
                
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
}
