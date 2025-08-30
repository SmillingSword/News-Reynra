<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Services\ContentRewriterService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestScrapingCommand extends Command
{
    protected $signature = 'news:test-scraping';
    protected $description = 'Test scraping functionality with sample data';

    public function handle(): int
    {
        $this->info('🧪 Testing news scraping functionality...');

        // Sample gaming news data (simulating scraped content)
        $sampleNews = [
            [
                'title' => 'Mobile Legends: Bang Bang Season 30 Resmi Dimulai dengan Hero Baru Zhuxin',
                'content' => 'Moonton resmi mengumumkan dimulainya Season 30 Mobile Legends: Bang Bang yang menghadirkan hero baru bernama Zhuxin. Hero dengan role Fighter ini memiliki kemampuan unik yang dapat mengubah jalannya pertandingan. Zhuxin dilengkapi dengan skill yang dapat memberikan damage besar dan mobilitas tinggi, membuatnya menjadi pilihan menarik untuk para player yang menyukai gameplay agresif. Season 30 juga menghadirkan berbagai update gameplay, item baru, dan penyesuaian balance untuk hero-hero existing.',
                'author' => 'Gaming News Bot',
                'source_url' => 'https://example.com/ml-season-30',
            ],
            [
                'title' => 'PUBG Mobile Hadirkan Mode Baru Metro Royale 2.0 dengan Fitur Survival Enhanced',
                'content' => 'Tencent Games mengumumkan peluncuran Metro Royale 2.0 untuk PUBG Mobile yang menghadirkan pengalaman survival yang lebih menantang. Mode baru ini menggabungkan elemen battle royale dengan survival horror, di mana player harus mengumpulkan resources, crafting equipment, dan bertahan hidup dari ancaman zombie dan player lain. Metro Royale 2.0 juga menghadirkan sistem progression yang lebih mendalam dan reward eksklusif untuk player yang berhasil survive.',
                'author' => 'Gaming News Bot',
                'source_url' => 'https://example.com/pubg-metro-royale',
            ],
            [
                'title' => 'Valorant Episode 8 Act 1 Menghadirkan Agent Baru Clove dengan Kemampuan Smoke Controller',
                'content' => 'Riot Games resmi memperkenalkan Agent baru Valorant bernama Clove dalam Episode 8 Act 1. Clove adalah Controller yang memiliki kemampuan unik dalam mengatur smoke dan memberikan support untuk tim. Kemampuan signature-nya dapat menciptakan smoke wall yang dapat diatur secara fleksibel, sementara ultimate-nya memberikan buff untuk seluruh tim. Agent asal Skotlandia ini diharapkan dapat mengubah meta Controller di competitive scene Valorant.',
                'author' => 'Gaming News Bot',
                'source_url' => 'https://example.com/valorant-clove',
            ]
        ];

        $contentRewriter = app(ContentRewriterService::class);
        $createdCount = 0;

        foreach ($sampleNews as $news) {
            try {
                // Simulate content rewriting
                $rewrittenContent = $contentRewriter->rewriteContent(
                    $news['content'],
                    $news['title'],
                    'gaming'
                );

                // Use first available user as author (or create if none exists)
                $user = \App\Models\User::first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => 'Gaming Bot',
                        'email' => 'bot@gaming.com',
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                    ]);
                }

                // Find or create author linked to user
                $author = Author::firstOrCreate(
                    ['display_name' => $news['author']],
                    [
                        'user_id' => $user->id,
                        'bio' => 'Bot penulis konten gaming dan esports Indonesia.',
                        'is_active' => true,
                    ]
                );

                // Create article
                $article = Article::create([
                    'title' => $news['title'],
                    'content' => $rewrittenContent['content'],
                    'excerpt' => $rewrittenContent['excerpt'],
                    'author_id' => $author->id,
                    'type' => 'news',
                    'status' => 'draft',
                    'published_at' => now(),
                    'meta' => [
                        'original_url' => $news['source_url'],
                        'scraped_at' => now()->toISOString(),
                        'rewritten' => true,
                        'test_data' => true,
                    ],
                ]);

                $this->line("✅ Created article: {$article->title}");
                $createdCount++;

            } catch (\Exception $e) {
                $this->error("❌ Failed to create article: {$news['title']} - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("🎉 Test completed! Created {$createdCount} test articles.");
        $this->line("📝 Articles are created as drafts and can be reviewed in admin panel.");
        $this->line("🔍 Check your website to see the new articles!");

        return self::SUCCESS;
    }
}
