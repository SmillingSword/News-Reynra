<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use Carbon\Carbon;

class PublishDraftArticlesCommand extends Command
{
    protected $signature = 'articles:publish-drafts {--limit=50 : Maximum number of drafts to publish}';
    protected $description = 'Publish draft articles that are ready for publication';

    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info("📝 Publishing draft articles (limit: {$limit})...");
        
        // Find draft articles that are ready to be published
        $draftArticles = Article::where('status', 'draft')
            ->where('created_at', '<=', Carbon::now()->subMinutes(5)) // Wait 5 minutes before publishing
            ->whereNotNull('title')
            ->whereNotNull('content')
            ->where('title', '!=', '')
            ->where('content', '!=', '')
            ->limit($limit)
            ->get();
        
        if ($draftArticles->isEmpty()) {
            $this->info('✅ No draft articles found to publish.');
            return 0;
        }
        
        $publishedCount = 0;
        $errorCount = 0;
        
        foreach ($draftArticles as $article) {
            try {
                // Validate article has minimum required content
                if (strlen(strip_tags($article->content)) < 100) {
                    $this->warn("⚠️  Skipping article '{$article->title}' - content too short");
                    continue;
                }
                
                // Update status to published
                $article->update([
                    'status' => 'published',
                    'published_at' => Carbon::now()
                ]);
                
                $publishedCount++;
                $this->line("✅ Published: {$article->title}");
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("❌ Failed to publish '{$article->title}': {$e->getMessage()}");
                
                Log::error('Failed to publish draft article', [
                    'article_id' => $article->id,
                    'title' => $article->title,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Summary
        $this->info("📊 Publishing Summary:");
        $this->line("   ✅ Published: {$publishedCount} articles");
        
        if ($errorCount > 0) {
            $this->line("   ❌ Errors: {$errorCount} articles");
        }
        
        // Log summary
        Log::info('Draft Articles Published', [
            'published_count' => $publishedCount,
            'error_count' => $errorCount,
            'total_processed' => $draftArticles->count()
        ]);
        
        $this->info('✅ Draft publishing completed!');
        
        return 0;
    }
}
