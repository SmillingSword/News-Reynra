<?php

namespace App\Jobs;

use App\Models\NewsSource;
use App\Services\NewsScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScrapeNewsSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public NewsSource $newsSource
    ) {
        // Set queue based on source priority
        $this->onQueue($newsSource->trust_score >= 8 ? 'high' : 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(NewsScraperService $scraperService): void
    {
        Log::info("Starting scrape job for news source: {$this->newsSource->name}");

        try {
            // Check if source is still active and ready for scraping
            if (!$this->newsSource->isReadyForScraping()) {
                Log::info("News source {$this->newsSource->name} is not ready for scraping, skipping");
                return;
            }

            // Perform the scraping
            $job = $scraperService->scrapeNewsSource($this->newsSource);

            Log::info("Completed scrape job for {$this->newsSource->name}", [
                'job_id' => $job->id,
                'status' => $job->status,
                'articles_created' => $job->articles_created,
                'articles_updated' => $job->articles_updated,
            ]);

        } catch (\Exception $e) {
            Log::error("Scrape job failed for {$this->newsSource->name}: " . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Scrape job permanently failed for {$this->newsSource->name}", [
            'exception' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Update news source to indicate failure
        $this->newsSource->updateScrapingStats(false);
        
        // Optionally, disable auto-scraping if too many failures
        if ($this->newsSource->failed_scrapes >= 5) {
            $this->newsSource->update(['auto_scraping_enabled' => false]);
            Log::warning("Disabled auto-scraping for {$this->newsSource->name} due to repeated failures");
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [60, 300, 900]; // 1 minute, 5 minutes, 15 minutes
    }

    /**
     * Determine if the job should be retried based on the exception.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'scraping',
            'news-source:' . $this->newsSource->id,
            'gaming:' . ($this->newsSource->is_gaming_source ? 'yes' : 'no'),
        ];
    }
}
