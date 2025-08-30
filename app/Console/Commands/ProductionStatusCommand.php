<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\NewsSource;
use App\Models\ScrapingJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductionStatusCommand extends Command
{
    protected $signature = 'production:status';
    protected $description = 'Show production status and statistics';

    public function handle(): int
    {
        $this->info('🎯 NEWS REYNRA PRODUCTION STATUS');
        $this->newLine();
        
        // Articles Statistics
        $this->line('📰 ARTICLES STATISTICS:');
        $totalArticles = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles = Article::where('status', 'draft')->count();
        $todayArticles = Article::whereDate('created_at', today())->count();
        $scrapedArticles = Article::whereNotNull('meta->original_source')->count();
        
        $this->line("- Total Articles: {$totalArticles}");
        $this->line("- Published: {$publishedArticles}");
        $this->line("- Drafts: {$draftArticles}");
        $this->line("- Created Today: {$todayArticles}");
        $this->line("- Scraped Articles: {$scrapedArticles}");
        $this->newLine();
        
        // News Sources Status
        $this->line('🌐 NEWS SOURCES STATUS:');
        $sources = NewsSource::where('is_active', true)->get();
        foreach ($sources as $source) {
            $lastScrape = ScrapingJob::where('news_source_id', $source->id)
                ->latest()
                ->first();
            
            $status = $lastScrape ? 
                ($lastScrape->status === 'completed' ? '✅' : 
                ($lastScrape->status === 'failed' ? '❌' : '⏳')) : '⚪';
            
            $lastTime = $lastScrape ? $lastScrape->created_at->diffForHumans() : 'Never';
            $this->line("- {$status} {$source->name}: {$lastTime}");
        }
        $this->newLine();
        
        // Recent Scraping Jobs
        $this->line('🔄 RECENT SCRAPING JOBS:');
        $recentJobs = ScrapingJob::with('newsSource')
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($recentJobs as $job) {
            $status = match($job->status) {
                'completed' => '✅',
                'failed' => '❌',
                'processing' => '⏳',
                default => '⚪'
            };
            
            $articlesCount = $job->articles_created ?? 0;
            $this->line("- {$status} {$job->newsSource->name}: {$articlesCount} articles ({$job->created_at->diffForHumans()})");
        }
        $this->newLine();
        
        // System Health
        $this->line('💚 SYSTEM HEALTH:');
        
        // Check queue worker
        $queueWorkerRunning = $this->checkQueueWorker();
        $this->line("- Queue Worker: " . ($queueWorkerRunning ? '✅ Running' : '❌ Stopped'));
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->line("- Database: ✅ Connected");
        } catch (\Exception $e) {
            $this->line("- Database: ❌ Connection Failed");
        }
        
        // Check recent activity
        $recentActivity = Article::where('created_at', '>=', now()->subHours(2))->count();
        $this->line("- Recent Activity: " . ($recentActivity > 0 ? "✅ {$recentActivity} articles in last 2h" : "⚠️ No recent activity"));
        
        $this->newLine();
        
        // Performance Metrics
        $this->line('📊 PERFORMANCE METRICS:');
        
        // Calculate average length (compatible with SQLite and MySQL)
        $articles = Article::whereNotNull('content')->get();
        $avgWordsPerArticle = $articles->count() > 0 ? 
            $articles->avg(function($article) { return strlen($article->content); }) : 0;
        
        $this->line("- Average Article Length: " . number_format($avgWordsPerArticle) . " characters");
        
        $successRate = $recentJobs->count() > 0 ? 
            ($recentJobs->where('status', 'completed')->count() / $recentJobs->count() * 100) : 0;
        $this->line("- Scraping Success Rate: " . number_format($successRate, 1) . "%");
        
        // Articles with SEO hashtags
        $articlesWithHashtags = Article::whereNotNull('meta->rewritten')->count();
        $this->line("- Articles with SEO Enhancement: {$articlesWithHashtags}");
        
        return self::SUCCESS;
    }
    
    private function checkQueueWorker(): bool
    {
        // Check if queue worker service is running (Linux)
        $output = shell_exec('systemctl is-active news-reynra-worker 2>/dev/null');
        return trim($output) === 'active';
    }
}
