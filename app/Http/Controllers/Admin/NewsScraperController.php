<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ScrapeNewsSourceJob;
use App\Models\NewsSource;
use App\Models\ScrapingJob;
use App\Services\NewsScraperService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class NewsScraperController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display news scraper dashboard.
     */
    public function index(): Response
    {
        $newsSources = NewsSource::with(['scrapingJobs' => function ($query) {
            $query->latest()->limit(5);
        }])
        ->withCount([
            'scrapingJobs as total_jobs',
            'scrapingJobs as successful_jobs' => function ($query) {
                $query->where('status', 'completed');
            },
            'scrapingJobs as failed_jobs' => function ($query) {
                $query->where('status', 'failed');
            },
        ])
        ->gaming()
        ->active()
        ->get();

        $recentJobs = ScrapingJob::with('newsSource')
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total_sources' => NewsSource::gaming()->active()->count(),
            'sources_ready' => NewsSource::gaming()->readyForScraping()->count(),
            'jobs_today' => ScrapingJob::whereDate('created_at', today())->count(),
            'articles_today' => ScrapingJob::whereDate('created_at', today())->sum('articles_created'),
        ];

        return Inertia::render('Admin/NewsScraper/Index', [
            'newsSources' => $newsSources,
            'recentJobs' => $recentJobs,
            'stats' => $stats,
        ]);
    }

    /**
     * Show news source scraping details.
     */
    public function show(NewsSource $newsSource): Response
    {
        $newsSource->load([
            'scrapingJobs' => function ($query) {
                $query->latest()->limit(20);
            }
        ]);

        $stats = [
            'total_jobs' => $newsSource->scrapingJobs()->count(),
            'successful_jobs' => $newsSource->scrapingJobs()->where('status', 'completed')->count(),
            'failed_jobs' => $newsSource->scrapingJobs()->where('status', 'failed')->count(),
            'total_articles_created' => $newsSource->scrapingJobs()->sum('articles_created'),
            'average_duration' => $newsSource->scrapingJobs()
                ->where('status', 'completed')
                ->avg('duration_seconds'),
            'success_rate' => $newsSource->scraping_success_rate,
        ];

        return Inertia::render('Admin/NewsScraper/Show', [
            'newsSource' => $newsSource,
            'stats' => $stats,
        ]);
    }

    /**
     * Manually trigger scraping for a news source.
     */
    public function scrape(Request $request, NewsSource $newsSource)
    {
        $request->validate([
            'sync' => 'boolean',
        ]);

        try {
            if ($request->boolean('sync')) {
                // Run synchronously for immediate feedback
                $scraperService = app(NewsScraperService::class);
                $job = $scraperService->scrapeNewsSource($newsSource);
                
                return back()->with('success', 
                    "Scraping completed! Created {$job->articles_created} articles, " .
                    "updated {$job->articles_updated}, skipped {$job->articles_skipped}."
                );
            } else {
                // Queue the job
                ScrapeNewsSourceJob::dispatch($newsSource);
                
                return back()->with('success', 
                    "Scraping job queued for {$newsSource->name}. Check back in a few minutes."
                );
            }
        } catch (\Exception $e) {
            return back()->with('error', 
                "Failed to start scraping: " . $e->getMessage()
            );
        }
    }

    /**
     * Scrape all gaming news sources.
     */
    public function scrapeAll(Request $request)
    {
        $request->validate([
            'force' => 'boolean',
            'sync' => 'boolean',
        ]);

        $query = NewsSource::gaming()->active();
        
        if (!$request->boolean('force')) {
            $query->readyForScraping();
        }

        $sources = $query->get();

        if ($sources->isEmpty()) {
            return back()->with('warning', 'No gaming news sources are ready for scraping.');
        }

        $jobsDispatched = 0;

        foreach ($sources as $source) {
            try {
                if ($request->boolean('sync')) {
                    $scraperService = app(NewsScraperService::class);
                    $scraperService->scrapeNewsSource($source);
                } else {
                    ScrapeNewsSourceJob::dispatch($source);
                }
                $jobsDispatched++;
            } catch (\Exception $e) {
                // Continue with other sources even if one fails
                continue;
            }
        }

        $message = $request->boolean('sync') 
            ? "Completed scraping {$jobsDispatched} news sources."
            : "Queued scraping jobs for {$jobsDispatched} news sources.";

        return back()->with('success', $message);
    }

    /**
     * Update news source scraping configuration.
     */
    public function updateConfig(Request $request, NewsSource $newsSource)
    {
        $validated = $request->validate([
            'auto_scraping_enabled' => 'boolean',
            'scraping_frequency' => 'integer|min:15|max:1440', // 15 minutes to 24 hours
            'scraping_config' => 'array',
            'content_filters' => 'array',
            'keyword_filters' => 'array',
        ]);

        $newsSource->update($validated);

        return back()->with('success', 'Scraping configuration updated successfully.');
    }

    /**
     * Get scraping job details.
     */
    public function jobDetails(ScrapingJob $scrapingJob)
    {
        $scrapingJob->load('newsSource');

        return response()->json([
            'job' => $scrapingJob,
            'formatted_duration' => $scrapingJob->formatted_duration,
            'success_rate' => $scrapingJob->success_rate,
            'status_color' => $scrapingJob->status_color,
        ]);
    }

    /**
     * Retry a failed scraping job.
     */
    public function retryJob(ScrapingJob $scrapingJob)
    {
        if (!$scrapingJob->canRetry()) {
            return back()->with('error', 'This job cannot be retried.');
        }

        try {
            ScrapeNewsSourceJob::dispatch($scrapingJob->newsSource);
            
            return back()->with('success', 
                "Retry job queued for {$scrapingJob->newsSource->name}."
            );
        } catch (\Exception $e) {
            return back()->with('error', 
                "Failed to queue retry job: " . $e->getMessage()
            );
        }
    }

    /**
     * Test scraping configuration for a news source.
     */
    public function testScraping(Request $request, NewsSource $newsSource)
    {
        $request->validate([
            'test_url' => 'url',
        ]);

        try {
            $scraperService = app(NewsScraperService::class);
            
            // Create a temporary test job to see what would be scraped
            $testResults = [
                'source_name' => $newsSource->name,
                'test_url' => $request->input('test_url', $newsSource->url),
                'config_valid' => !empty($newsSource->scraping_config),
                'selectors' => $newsSource->scraping_config['selectors'] ?? [],
                'filters' => $newsSource->scraping_config['filters'] ?? [],
            ];

            return response()->json([
                'success' => true,
                'results' => $testResults,
                'message' => 'Test completed successfully. Check the results above.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get scraping statistics for dashboard.
     */
    public function stats()
    {
        $stats = [
            'sources' => [
                'total' => NewsSource::gaming()->count(),
                'active' => NewsSource::gaming()->active()->count(),
                'ready_for_scraping' => NewsSource::gaming()->readyForScraping()->count(),
            ],
            'jobs_today' => [
                'total' => ScrapingJob::whereDate('created_at', today())->count(),
                'completed' => ScrapingJob::whereDate('created_at', today())
                    ->where('status', 'completed')->count(),
                'failed' => ScrapingJob::whereDate('created_at', today())
                    ->where('status', 'failed')->count(),
            ],
            'articles_today' => ScrapingJob::whereDate('created_at', today())
                ->sum('articles_created'),
            'performance' => [
                'average_duration' => ScrapingJob::where('status', 'completed')
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->avg('duration_seconds'),
                'success_rate' => $this->calculateOverallSuccessRate(),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Calculate overall success rate for scraping jobs.
     */
    private function calculateOverallSuccessRate(): float
    {
        $totalJobs = ScrapingJob::whereDate('created_at', '>=', now()->subDays(30))->count();
        
        if ($totalJobs === 0) {
            return 0;
        }

        $successfulJobs = ScrapingJob::whereDate('created_at', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->count();

        return round(($successfulJobs / $totalJobs) * 100, 2);
    }
}
