<?php

namespace App\Services;

use App\Models\NewsSource;
use App\Models\ScrapingJob;
use App\Models\Article;
use App\Models\Author;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsScraperService
{
    private Client $httpClient;
    private ContentRewriterService $contentRewriter;

    public function __construct(ContentRewriterService $contentRewriter)
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                'Accept-Encoding' => 'gzip, deflate',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ]);
        
        $this->contentRewriter = $contentRewriter;
    }

    /**
     * Scrape news from a specific source.
     */
    public function scrapeNewsSource(NewsSource $newsSource): ScrapingJob
    {
        $job = ScrapingJob::create([
            'news_source_id' => $newsSource->id,
            'status' => 'pending',
            'type' => 'scheduled',
        ]);

        try {
            $job->markAsStarted();
            
            Log::info("Starting scrape for news source: {$newsSource->name}");
            
            $articles = $this->extractArticles($newsSource);
            $processedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($articles as $articleData) {
                try {
                    $result = $this->processArticle($articleData, $newsSource);
                    
                    switch ($result['action']) {
                        case 'created':
                            $createdCount++;
                            break;
                        case 'updated':
                            $updatedCount++;
                            break;
                        case 'skipped':
                            $skippedCount++;
                            break;
                    }
                    
                    $processedCount++;
                    
                    // Add delay between processing articles
                    $delay = $newsSource->scraping_config['limits']['request_delay'] ?? 2;
                    sleep($delay);
                    
                } catch (\Exception $e) {
                    Log::error("Error processing article: " . $e->getMessage(), [
                        'article_data' => $articleData,
                        'news_source' => $newsSource->name,
                    ]);
                    $skippedCount++;
                }
            }

            $job->markAsCompleted([
                'articles_found' => count($articles),
                'articles_processed' => $processedCount,
                'articles_created' => $createdCount,
                'articles_updated' => $updatedCount,
                'articles_skipped' => $skippedCount,
            ]);

            $newsSource->updateScrapingStats(true);
            
            Log::info("Completed scrape for {$newsSource->name}: {$createdCount} created, {$updatedCount} updated, {$skippedCount} skipped");

        } catch (\Exception $e) {
            $job->markAsFailed($e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $newsSource->updateScrapingStats(false);
            
            Log::error("Failed to scrape {$newsSource->name}: " . $e->getMessage());
        }

        return $job;
    }

    /**
     * Extract articles from news source.
     */
    private function extractArticles(NewsSource $newsSource): array
    {
        $articles = [];
        $config = $newsSource->scraping_config;
        
        // Try RSS first if available
        if ($newsSource->rss_url) {
            try {
                $articles = array_merge($articles, $this->extractFromRSS($newsSource->rss_url, $config));
            } catch (\Exception $e) {
                Log::warning("RSS extraction failed for {$newsSource->name}: " . $e->getMessage());
            }
        }

        // Fallback to HTML scraping
        if (empty($articles)) {
            $articles = $this->extractFromHTML($newsSource->url, $config);
        }

        // Apply filters
        $articles = $this->applyFilters($articles, $newsSource);

        // Limit articles per scrape
        $maxArticles = $config['limits']['max_articles_per_scrape'] ?? 20;
        return array_slice($articles, 0, $maxArticles);
    }

    /**
     * Extract articles from RSS feed.
     */
    private function extractFromRSS(string $rssUrl, array $config): array
    {
        $response = $this->httpClient->get($rssUrl);
        $xml = simplexml_load_string($response->getBody()->getContents());
        
        $articles = [];
        
        foreach ($xml->channel->item as $item) {
            $articles[] = [
                'title' => (string) $item->title,
                'url' => (string) $item->link,
                'excerpt' => (string) ($item->description ?? ''),
                'published_at' => $this->parseDate((string) $item->pubDate),
                'source_type' => 'rss',
            ];
        }

        return $articles;
    }

    /**
     * Extract articles from HTML page.
     */
    private function extractFromHTML(string $url, array $config): array
    {
        $response = $this->httpClient->get($url);
        $crawler = new Crawler($response->getBody()->getContents());
        
        $articles = [];
        $selectors = $config['selectors'] ?? [];
        
        // Find article links
        $linkSelector = $selectors['article_links'] ?? 'a[href*="/news/"], a[href*="/artikel/"]';
        
        $crawler->filter($linkSelector)->each(function (Crawler $node) use (&$articles, $url) {
            $href = $node->attr('href');
            
            if ($href) {
                // Convert relative URLs to absolute
                if (!str_starts_with($href, 'http')) {
                    $href = rtrim($url, '/') . '/' . ltrim($href, '/');
                }
                
                $articles[] = [
                    'title' => trim($node->text()),
                    'url' => $href,
                    'source_type' => 'html',
                ];
            }
        });

        // Get full article content for each link
        $detailedArticles = [];
        foreach ($articles as $article) {
            try {
                $detailedArticle = $this->extractArticleDetails($article['url'], $config);
                if ($detailedArticle) {
                    $detailedArticles[] = array_merge($article, $detailedArticle);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to extract details for {$article['url']}: " . $e->getMessage());
            }
        }

        return $detailedArticles;
    }

    /**
     * Extract detailed article content from URL.
     */
    private function extractArticleDetails(string $url, array $config): ?array
    {
        try {
            $response = $this->httpClient->get($url);
            $crawler = new Crawler($response->getBody()->getContents());
            
            $selectors = $config['selectors'] ?? [];
            
            // Extract title
            $title = $this->extractText($crawler, $selectors['title'] ?? 'h1');
            
            // Extract content
            $content = $this->extractText($crawler, $selectors['content'] ?? '.content');
            
            // Extract excerpt
            $excerpt = $this->extractText($crawler, $selectors['excerpt'] ?? '.excerpt');
            
            // Extract image
            $image = $this->extractAttribute($crawler, $selectors['image'] ?? 'img', 'src');
            
            // Extract date
            $dateText = $this->extractText($crawler, $selectors['date'] ?? '.date');
            $publishedAt = $this->parseDate($dateText);
            
            // Extract author
            $author = $this->extractText($crawler, $selectors['author'] ?? '.author');

            return [
                'title' => $title,
                'content' => $content,
                'excerpt' => $excerpt ?: Str::limit(strip_tags($content), 200),
                'featured_image_url' => $image,
                'published_at' => $publishedAt,
                'author_name' => $author,
            ];
            
        } catch (\Exception $e) {
            Log::error("Error extracting article details from {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract text content using CSS selector.
     */
    private function extractText(Crawler $crawler, string $selector): string
    {
        try {
            $node = $crawler->filter($selector)->first();
            return $node->count() > 0 ? trim($node->text()) : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Extract attribute value using CSS selector.
     */
    private function extractAttribute(Crawler $crawler, string $selector, string $attribute): string
    {
        try {
            $node = $crawler->filter($selector)->first();
            return $node->count() > 0 ? $node->attr($attribute) : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate(?string $dateString): ?Carbon
    {
        if (!$dateString) {
            return null;
        }

        try {
            // Try common Indonesian date formats
            $formats = [
                'Y-m-d H:i:s',
                'd/m/Y H:i',
                'd-m-Y H:i',
                'Y-m-d',
                'd/m/Y',
                'd-m-Y',
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $dateString);
                } catch (\Exception $e) {
                    continue;
                }
            }

            // Fallback to Carbon's flexible parsing
            return Carbon::parse($dateString);
            
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$dateString}");
            return now();
        }
    }

    /**
     * Apply content filters to articles.
     */
    private function applyFilters(array $articles, NewsSource $newsSource): array
    {
        $filters = $newsSource->content_filters ?? [];
        $keywordFilters = $newsSource->keyword_filters ?? [];
        
        return array_filter($articles, function ($article) use ($filters, $keywordFilters) {
            // Check minimum content length
            $minLength = $filters['min_content_length'] ?? 200;
            if (strlen($article['content'] ?? '') < $minLength) {
                return false;
            }

            // Check exclude patterns
            $excludePatterns = $filters['exclude_patterns'] ?? [];
            foreach ($excludePatterns as $pattern) {
                if (stripos($article['title'] ?? '', $pattern) !== false || 
                    stripos($article['content'] ?? '', $pattern) !== false) {
                    return false;
                }
            }

            // Check required keywords for gaming content
            $requiredKeywords = $filters['required_keywords'] ?? [];
            if (!empty($requiredKeywords)) {
                $hasKeyword = false;
                $searchText = strtolower(($article['title'] ?? '') . ' ' . ($article['content'] ?? ''));
                
                foreach ($requiredKeywords as $keyword) {
                    if (stripos($searchText, $keyword) !== false) {
                        $hasKeyword = true;
                        break;
                    }
                }
                
                if (!$hasKeyword) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Process and save article.
     */
    private function processArticle(array $articleData, NewsSource $newsSource): array
    {
        // Check if article already exists
        $existingArticle = Article::where('title', $articleData['title'])
            ->orWhere('slug', Str::slug($articleData['title']))
            ->first();

        if ($existingArticle) {
            return ['action' => 'skipped', 'reason' => 'duplicate'];
        }

        // Rewrite content using AI
        $rewrittenContent = $this->contentRewriter->rewriteContent(
            $articleData['content'] ?? '',
            $articleData['title'] ?? '',
            'gaming'
        );

        // Find or create author
        $author = $this->findOrCreateAuthor($articleData['author_name'] ?? 'Admin');

        // Create article
        $article = Article::create([
            'title' => $articleData['title'],
            'content' => $rewrittenContent['content'],
            'excerpt' => $rewrittenContent['excerpt'] ?? $articleData['excerpt'],
            'author_id' => $author->id,
            'news_source_id' => $newsSource->id,
            'type' => 'news',
            'status' => 'draft', // Require manual review
            'published_at' => $articleData['published_at'] ?? now(),
            'meta' => [
                'original_url' => $articleData['url'] ?? null,
                'scraped_at' => now()->toISOString(),
                'rewritten' => true,
            ],
        ]);

        // Add gaming categories if it's a gaming source
        if ($newsSource->is_gaming_source) {
            $this->assignGamingCategories($article, $articleData);
        }

        return ['action' => 'created', 'article_id' => $article->id];
    }

    /**
     * Find or create author.
     */
    private function findOrCreateAuthor(string $name): Author
    {
        return Author::firstOrCreate(
            ['display_name' => $name],
            [
                'bio' => "Penulis konten gaming dan esports.",
                'is_active' => true,
            ]
        );
    }

    /**
     * Assign gaming categories to article.
     */
    private function assignGamingCategories(Article $article, array $articleData): void
    {
        $content = strtolower($articleData['title'] . ' ' . $articleData['content']);
        
        // Gaming category mapping
        $categoryMappings = [
            'mobile_games' => ['mobile legends', 'pubg mobile', 'free fire', 'mobile game'],
            'pc_games' => ['valorant', 'dota 2', 'csgo', 'pc game'],
            'esports' => ['tournament', 'championship', 'esport', 'kompetisi'],
            'reviews' => ['review', 'ulasan', 'rating'],
        ];

        foreach ($categoryMappings as $categorySlug => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    // Find and attach category
                    $category = \App\Models\Category::where('slug', $categorySlug)->first();
                    if ($category) {
                        $article->categories()->attach($category->id);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Get scraping statistics for a news source.
     */
    public function getScrapingStats(NewsSource $newsSource): array
    {
        $recentJobs = $newsSource->scrapingJobs()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return [
            'total_jobs' => $recentJobs->count(),
            'successful_jobs' => $recentJobs->where('status', 'completed')->count(),
            'failed_jobs' => $recentJobs->where('status', 'failed')->count(),
            'total_articles_created' => $recentJobs->sum('articles_created'),
            'average_duration' => $recentJobs->where('status', 'completed')->avg('duration_seconds'),
            'last_successful_scrape' => $newsSource->last_scraped_at,
            'next_scheduled_scrape' => $newsSource->next_scrape_at,
        ];
    }
}
