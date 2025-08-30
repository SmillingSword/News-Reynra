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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AutoContentCreatorCommand extends Command
{
    protected $signature = 'news:auto-create-content {--limit=3 : Number of articles to create}';
    protected $description = 'Automatically scrape, enhance with SEO & hashtags, and publish gaming news articles';

    public function handle()
    {
        $this->info('🚀 Starting automatic content creation...');
        
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

            foreach ($newsSources as $source) {
                if ($createdCount >= $limit) break;

                $this->info("🔍 Scraping from: {$source->name} ({$source->url})");
                
                // Scrape articles from source
                $articles = $this->scrapeFromSource($source);
                
                foreach ($articles as $articleData) {
                    if ($createdCount >= $limit) break;
                    
                    // Check if article already exists
                    if ($this->articleExists($articleData['title'])) {
                        $this->warn("⚠️  Article already exists: {$articleData['title']}");
                        continue;
                    }
                    
                    $this->info("📝 Processing: " . Str::limit($articleData['title'], 50));
                    
                    // Enhance content with SEO and hashtags
                    $enhancedData = $this->enhanceContent($articleData);
                    
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
                    }
                }
            }

            $this->info("🎉 Content creation completed!");
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
                    
                    if (count($articles) >= 5) break; // Limit per source
                }
            }
            
            return $articles;
            
        } catch (\Exception $e) {
            $this->warn("⚠️  Error scraping {$source->name}: " . $e->getMessage());
            return [];
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

            // Extract content
            $contentNodes = $xpath->query('//div[contains(@class, "entry-content")] | //div[contains(@class, "post-content")] | //article//p');
            $content = '';
            
            foreach ($contentNodes as $node) {
                $content .= $dom->saveHTML($node) . "\n";
            }

            // Extract featured image
            $imageNode = $xpath->query('//meta[@property="og:image"]/@content | //img[contains(@class, "featured")]/@src')->item(0);
            $featuredImage = $imageNode ? $imageNode->nodeValue : null;

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

    private function enhanceContent($articleData)
    {
        // Generate gaming hashtags
        $hashtags = $this->generateGamingHashtags($articleData['title'], $articleData['content']);
        
        // Enhance title with SEO
        $seoTitle = $this->enhanceTitle($articleData['title']);
        
        // Enhance content with hashtags and SEO
        $enhancedContent = $this->addHashtagsToContent($articleData['content'], $hashtags);
        
        // Generate meta description
        $metaDescription = $this->generateMetaDescription($articleData['excerpt'], $hashtags);

        return array_merge($articleData, [
            'title' => $seoTitle,
            'content' => $enhancedContent,
            'hashtags' => $hashtags,
            'meta_description' => $metaDescription,
            'slug' => Str::slug($seoTitle)
        ]);
    }

    private function generateGamingHashtags($title, $content)
    {
        $text = $title . ' ' . strip_tags($content);
        $hashtags = [];

        // Gaming keywords to hashtags mapping
        $gamingKeywords = [
            'mobile legends' => '#MobileLegends',
            'pubg' => '#PUBG',
            'valorant' => '#Valorant',
            'genshin impact' => '#GenshinImpact',
            'free fire' => '#FreeFire',
            'call of duty' => '#CallOfDuty',
            'minecraft' => '#Minecraft',
            'fortnite' => '#Fortnite',
            'league of legends' => '#LeagueOfLegends',
            'dota' => '#Dota2',
            'esports' => '#Esports',
            'gaming' => '#Gaming',
            'game' => '#Game',
            'update' => '#GameUpdate',
            'season' => '#NewSeason',
            'hero' => '#NewHero',
            'skin' => '#GameSkin',
            'tournament' => '#GamingTournament',
            'indonesia' => '#GamingIndonesia'
        ];

        foreach ($gamingKeywords as $keyword => $hashtag) {
            if (stripos($text, $keyword) !== false) {
                $hashtags[] = $hashtag;
            }
        }

        // Add general gaming hashtags
        $hashtags[] = '#GamingNews';
        $hashtags[] = '#NewsReynra';
        
        return array_unique(array_slice($hashtags, 0, 8)); // Limit to 8 hashtags
    }

    private function enhanceTitle($title)
    {
        // Add engaging prefixes for SEO
        $prefixes = [
            '🔥 BREAKING! ',
            '⚡ HOT NEWS! ',
            '🎮 GAMING UPDATE: ',
            '🚨 TERBARU! ',
            '💥 VIRAL! '
        ];
        
        // Don't add prefix if title already has emoji or exciting words
        if (!preg_match('/[🔥⚡🎮🚨💥]|BREAKING|HOT|TERBARU|VIRAL/i', $title)) {
            $title = $prefixes[array_rand($prefixes)] . $title;
        }
        
        return $title;
    }

    private function addHashtagsToContent($content, $hashtags)
    {
        if (empty($hashtags)) return $content;
        
        $hashtagString = implode(' ', $hashtags);
        
        // Add hashtags at the end of content
        $content .= "\n\n<div class='article-hashtags'>";
        $content .= "<p><strong>Tags:</strong> {$hashtagString}</p>";
        $content .= "</div>";
        
        return $content;
    }

    private function generateMetaDescription($excerpt, $hashtags)
    {
        $description = strip_tags($excerpt);
        
        // Add some hashtags to meta description
        if (!empty($hashtags)) {
            $topHashtags = array_slice($hashtags, 0, 3);
            $description .= ' ' . implode(' ', $topHashtags);
        }
        
        return Str::limit($description, 155); // SEO optimal length
    }

    private function articleExists($title)
    {
        return Article::where('title', 'like', '%' . $title . '%')
                     ->orWhere('slug', Str::slug($title))
                     ->exists();
    }

    private function createArticle($data)
    {
        try {
            // Get or create category
            $category = Category::firstOrCreate(
                ['name' => 'Gaming News'],
                ['slug' => 'gaming-news', 'description' => 'Latest gaming news and updates']
            );

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
                'published_at' => null
            ]);

            // Attach category
            $article->categories()->attach($category->id);

            // Create and attach tags from hashtags
            foreach ($data['hashtags'] as $hashtag) {
                $tagName = ltrim($hashtag, '#');
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $article->tags()->attach($tag->id);
            }

            return $article;

        } catch (\Exception $e) {
            $this->error("❌ Error creating article: " . $e->getMessage());
            return null;
        }
    }
}
