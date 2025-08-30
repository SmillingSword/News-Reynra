<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeNewsSourceJob;
use App\Models\NewsSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScrapeNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'news:scrape 
                            {--source= : Specific news source ID or slug to scrape}
                            {--gaming : Only scrape gaming news sources}
                            {--force : Force scrape even if not scheduled}
                            {--sync : Run synchronously instead of queuing}
                            {--dry-run : Show what would be scraped without actually scraping}';

    /**
     * The console command description.
     */
    protected $description = 'Scrape news from configured sources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting news scraping process...');

        try {
            $sources = $this->getNewsSourcesForScraping();

            if ($sources->isEmpty()) {
                $this->warn('No news sources found for scraping.');
                return self::SUCCESS;
            }

            $this->info("Found {$sources->count()} news source(s) to scrape:");

            foreach ($sources as $source) {
                $this->line("  • {$source->name} ({$source->url})");
            }

            if ($this->option('dry-run')) {
                $this->info('🔍 Dry run completed. No actual scraping performed.');
                return self::SUCCESS;
            }

            $this->newLine();
            $this->info('📰 Starting scraping jobs...');

            $jobsDispatched = 0;
            $jobsSkipped = 0;

            foreach ($sources as $source) {
                if ($this->shouldScrapeSource($source)) {
                    if ($this->option('sync')) {
                        $this->info("🔄 Scraping {$source->name} synchronously...");
                        ScrapeNewsSourceJob::dispatchSync($source);
                    } else {
                        $this->info("📋 Queuing scrape job for {$source->name}...");
                        ScrapeNewsSourceJob::dispatch($source);
                    }
                    $jobsDispatched++;
                } else {
                    $this->line("⏭️  Skipping {$source->name} (not ready for scraping)");
                    $jobsSkipped++;
                }
            }

            $this->newLine();
            $this->info("✅ Scraping process completed!");
            $this->line("   Jobs dispatched: {$jobsDispatched}");
            $this->line("   Jobs skipped: {$jobsSkipped}");

            if ($jobsDispatched > 0 && !$this->option('sync')) {
                $this->info("💡 Jobs have been queued. Run 'php artisan queue:work' to process them.");
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error during scraping: " . $e->getMessage());
            Log::error('News scraping command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Get news sources for scraping based on command options.
     */
    private function getNewsSourcesForScraping()
    {
        $query = NewsSource::query()->active();

        // Filter by specific source
        if ($sourceId = $this->option('source')) {
            if (is_numeric($sourceId)) {
                $query->where('id', $sourceId);
            } else {
                $query->where('slug', $sourceId);
            }
        }

        // Filter by gaming sources only
        if ($this->option('gaming')) {
            $query->gaming();
        }

        // If not forcing, only get sources ready for scraping
        if (!$this->option('force')) {
            $query->readyForScraping();
        }

        return $query->orderBy('trust_score', 'desc')->get();
    }

    /**
     * Check if a source should be scraped.
     */
    private function shouldScrapeSource(NewsSource $source): bool
    {
        // Always scrape if forced
        if ($this->option('force')) {
            return true;
        }

        // Check if source is ready for scraping
        return $source->isReadyForScraping();
    }
}
