<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class PublishDraftArticlesCommand extends Command
{
    protected $signature = 'articles:publish-drafts {--confirm : Skip confirmation prompt}';
    protected $description = 'Publish all draft articles that are ready for publication';

    public function handle(): int
    {
        $draftArticles = Article::where('status', 'draft')->get();

        if ($draftArticles->isEmpty()) {
            $this->info('📝 No draft articles found.');
            return self::SUCCESS;
        }

        $this->info("📋 Found {$draftArticles->count()} draft articles:");
        
        foreach ($draftArticles as $article) {
            $this->line("  • {$article->title}");
        }

        if (!$this->option('confirm')) {
            if (!$this->confirm('Do you want to publish all these draft articles?')) {
                $this->info('❌ Operation cancelled.');
                return self::SUCCESS;
            }
        }

        $publishedCount = 0;
        foreach ($draftArticles as $article) {
            try {
                $article->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);
                
                $this->line("✅ Published: {$article->title}");
                $publishedCount++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to publish: {$article->title} - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("🎉 Successfully published {$publishedCount} articles!");
        $this->line("🌐 Articles are now visible on your website.");

        return self::SUCCESS;
    }
}
