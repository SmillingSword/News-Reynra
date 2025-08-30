<?php

namespace Database\Seeders;

use App\Models\NewsSource;
use Illuminate\Database\Seeder;

class GamingNewsSourceSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $gamingSources = [
            [
                'name' => 'Dunia Games',
                'url' => 'https://duniagames.co.id',
                'rss_url' => 'https://duniagames.co.id/discover/rss',
                'description' => 'Portal gaming terbesar di Indonesia dengan berita game, esports, dan review terlengkap.',
                'trust_score' => 9,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 30, // 30 minutes
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['mobile_games', 'pc_games', 'esports', 'news'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.post-item a, .article-item a, .news-item a',
                        'title' => 'h1.entry-title, h1.post-title, .article-title h1',
                        'content' => '.entry-content, .post-content, .article-body',
                        'excerpt' => '.excerpt, .post-excerpt, .article-excerpt',
                        'image' => '.featured-image img, .post-thumbnail img',
                        'date' => '.post-date, .entry-date, time',
                        'author' => '.author-name, .post-author, .byline',
                    ],
                    'filters' => [
                        'min_content_length' => 300,
                        'exclude_patterns' => ['iklan', 'advertisement', 'sponsored', 'promosi'],
                        'required_keywords' => ['game', 'gaming', 'esport', 'mobile legends', 'pubg', 'free fire', 'valorant'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 15,
                        'request_delay' => 2,
                    ],
                ],
                'content_filters' => [
                    'min_word_count' => 100,
                    'exclude_categories' => ['advertisement', 'sponsored'],
                ],
                'keyword_filters' => [
                    'include' => ['mobile legends', 'pubg mobile', 'free fire', 'valorant', 'dota 2', 'esports', 'tournament'],
                    'exclude' => ['judi', 'betting', 'casino'],
                ],
            ],
            [
                'name' => 'Esports.id',
                'url' => 'https://esports.id',
                'description' => 'Media esports Indonesia yang fokus pada berita turnamen, tim, dan pemain esports.',
                'trust_score' => 8,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 45,
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['esports', 'tournaments', 'news'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.post-title a, .entry-title a, .article-link',
                        'title' => 'h1.post-title, h1.entry-title, .article-title',
                        'content' => '.post-content, .entry-content, .article-content',
                        'excerpt' => '.post-excerpt, .entry-summary',
                        'image' => '.post-thumbnail img, .featured-image img',
                        'date' => '.post-date, .published-date',
                        'author' => '.author, .post-author',
                    ],
                    'filters' => [
                        'min_content_length' => 250,
                        'exclude_patterns' => ['iklan', 'ads'],
                        'required_keywords' => ['esport', 'tournament', 'championship', 'team', 'player'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 12,
                        'request_delay' => 3,
                    ],
                ],
            ],
            [
                'name' => 'GGWP.id',
                'url' => 'https://ggwp.id',
                'description' => 'Platform gaming Indonesia dengan fokus pada mobile gaming dan esports.',
                'trust_score' => 7,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 60,
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['mobile_games', 'esports', 'reviews'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.article-card a, .post-link, .news-link',
                        'title' => 'h1, .article-title, .post-title',
                        'content' => '.article-body, .post-body, .content',
                        'excerpt' => '.article-excerpt, .summary',
                        'image' => '.article-image img, .thumbnail img',
                        'date' => '.date, .timestamp, .published',
                        'author' => '.author-name, .writer',
                    ],
                    'filters' => [
                        'min_content_length' => 200,
                        'exclude_patterns' => ['sponsored'],
                        'required_keywords' => ['mobile', 'game', 'gaming', 'review'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 10,
                        'request_delay' => 2,
                    ],
                ],
            ],
            [
                'name' => 'MPL Indonesia',
                'url' => 'https://www.mpl.id',
                'description' => 'Official website Mobile Legends Professional League Indonesia.',
                'trust_score' => 9,
                'type' => 'official',
                'is_gaming_source' => true,
                'scraping_frequency' => 120, // 2 hours
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['esports', 'tournaments', 'mobile_legends'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.news-item a, .article-link, .post-link',
                        'title' => 'h1.title, .news-title, .article-title',
                        'content' => '.news-content, .article-content, .post-content',
                        'excerpt' => '.news-excerpt, .summary',
                        'image' => '.news-image img, .featured-img img',
                        'date' => '.news-date, .publish-date',
                        'author' => '.author, .publisher',
                    ],
                    'filters' => [
                        'min_content_length' => 150,
                        'exclude_patterns' => [],
                        'required_keywords' => ['mpl', 'mobile legends', 'tournament', 'team', 'season'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 8,
                        'request_delay' => 3,
                    ],
                ],
            ],
            [
                'name' => 'GameBrott',
                'url' => 'https://gamebrott.com',
                'description' => 'Media gaming Indonesia dengan berita game, review, dan tips gaming.',
                'trust_score' => 7,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 90,
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['pc_games', 'mobile_games', 'reviews', 'news'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.post-title a, .article-title a',
                        'title' => 'h1.entry-title, .post-title',
                        'content' => '.entry-content, .post-content',
                        'excerpt' => '.entry-excerpt, .post-excerpt',
                        'image' => '.post-thumbnail img, .featured-image img',
                        'date' => '.entry-date, .post-date',
                        'author' => '.author-name, .post-author',
                    ],
                    'filters' => [
                        'min_content_length' => 250,
                        'exclude_patterns' => ['iklan', 'sponsored'],
                        'required_keywords' => ['game', 'gaming', 'review', 'tips'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 12,
                        'request_delay' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Jalantikus Gaming',
                'url' => 'https://jalantikus.com/gaming',
                'description' => 'Section gaming dari Jalantikus dengan berita dan review game terbaru.',
                'trust_score' => 6,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 120,
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['mobile_games', 'pc_games', 'reviews'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.article-item a, .post-item a',
                        'title' => 'h1.article-title, .post-title',
                        'content' => '.article-content, .post-body',
                        'excerpt' => '.article-summary, .excerpt',
                        'image' => '.article-image img, .post-image img',
                        'date' => '.article-date, .post-date',
                        'author' => '.article-author, .author',
                    ],
                    'filters' => [
                        'min_content_length' => 200,
                        'exclude_patterns' => ['download', 'aplikasi'],
                        'required_keywords' => ['game', 'gaming', 'mobile', 'android'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 10,
                        'request_delay' => 2,
                    ],
                ],
            ],
            [
                'name' => 'OneEsport',
                'url' => 'https://www.oneesports.id',
                'rss_url' => 'https://www.oneesports.id/feed/',
                'description' => 'Platform esports terkemuka dengan berita, analisis, dan konten gaming terbaru.',
                'trust_score' => 8,
                'type' => 'media',
                'is_gaming_source' => true,
                'scraping_frequency' => 45, // 45 minutes
                'auto_scraping_enabled' => true,
                'gaming_categories' => ['esports', 'mobile_games', 'pc_games', 'news', 'tournaments'],
                'scraping_config' => [
                    'selectors' => [
                        'article_links' => '.post-title a, .entry-title a, .article-link, .news-item a',
                        'title' => 'h1.entry-title, h1.post-title, .article-title h1, .post-header h1',
                        'content' => '.entry-content, .post-content, .article-body, .content-area',
                        'excerpt' => '.entry-excerpt, .post-excerpt, .article-excerpt, .summary',
                        'image' => '.featured-image img, .post-thumbnail img, .article-image img, .wp-post-image',
                        'date' => '.entry-date, .post-date, .published-date, time.entry-date',
                        'author' => '.author-name, .post-author, .byline, .entry-author',
                    ],
                    'filters' => [
                        'min_content_length' => 300,
                        'exclude_patterns' => ['iklan', 'advertisement', 'sponsored', 'promosi', 'ads'],
                        'required_keywords' => ['esport', 'gaming', 'mobile legends', 'valorant', 'dota', 'pubg', 'free fire', 'tournament', 'championship'],
                    ],
                    'limits' => [
                        'max_articles_per_scrape' => 15,
                        'request_delay' => 2,
                    ],
                ],
                'content_filters' => [
                    'min_word_count' => 150,
                    'exclude_categories' => ['advertisement', 'sponsored', 'promosi'],
                ],
                'keyword_filters' => [
                    'include' => ['esports', 'mobile legends', 'valorant', 'dota 2', 'pubg mobile', 'free fire', 'tournament', 'championship', 'team', 'player', 'gaming'],
                    'exclude' => ['judi', 'betting', 'casino', 'slot'],
                ],
            ],
        ];

        foreach ($gamingSources as $sourceData) {
            // Only use fields that exist in the base migration
            $basicData = [
                'name' => $sourceData['name'],
                'url' => $sourceData['url'],
                'description' => $sourceData['description'],
                'trust_score' => $sourceData['trust_score'],
                'type' => $sourceData['type'],
                'is_active' => true,
                'slug' => \Illuminate\Support\Str::slug($sourceData['name']),
            ];
            
            NewsSource::updateOrCreate(
                ['url' => $sourceData['url']],
                $basicData
            );
        }

        $this->command->info('Gaming news sources seeded successfully!');
    }
}
