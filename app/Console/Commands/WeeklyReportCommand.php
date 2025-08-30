<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Article;
use App\Models\NewsSource;
use Carbon\Carbon;

class WeeklyReportCommand extends Command
{
    protected $signature = 'news:weekly-report';
    protected $description = 'Generate weekly content creation report';

    public function handle()
    {
        $this->info('📊 Generating Weekly Content Report...');
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        // Get weekly statistics
        $weeklyArticles = Article::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        $dailyAverage = round($weeklyArticles / 7, 1);
        $totalArticles = Article::count();
        
        // Get articles by day
        $dailyStats = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $count = Article::whereDate('created_at', $date)->count();
            $dailyStats[] = [
                'date' => $date->format('Y-m-d (D)'),
                'count' => $count
            ];
        }
        
        // Get top performing sources with actual article counts
        $topSources = NewsSource::withCount(['articles' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
        }])
        ->orderBy('articles_count', 'desc')
        ->limit(5)
        ->get();
        
        // Generate report
        $report = "
📊 NEWS REYNRA - WEEKLY CONTENT REPORT
=====================================
📅 Week: {$startOfWeek->format('M d')} - {$endOfWeek->format('M d, Y')}

📈 WEEKLY STATISTICS:
• Total articles created: {$weeklyArticles}
• Daily average: {$dailyAverage} articles
• Total articles in database: {$totalArticles}

📊 DAILY BREAKDOWN:
";
        
        foreach ($dailyStats as $stat) {
            $report .= "• {$stat['date']}: {$stat['count']} articles\n";
        }
        
        $report .= "\n🏆 TOP PERFORMING SOURCES:\n";
        foreach ($topSources as $index => $source) {
            $rank = $index + 1;
            $report .= "• #{$rank} {$source->name}: {$source->articles_count} articles\n";
        }
        
        $report .= "\n⚡ INTENSIVE AUTOMATION STATUS:
• Target: 15 articles/hour = 2,520/week
• Actual: {$weeklyArticles} articles
• Performance: " . round(($weeklyArticles / 2520) * 100, 1) . "%

🎯 NEXT WEEK GOALS:
• Maintain 15+ articles per hour
• Monitor server performance
• Optimize content quality

Generated: " . Carbon::now()->format('Y-m-d H:i:s') . "
";
        
        // Output report
        $this->line($report);
        
        // Log report
        Log::info('Weekly Content Report Generated', [
            'week_start' => $startOfWeek,
            'week_end' => $endOfWeek,
            'weekly_articles' => $weeklyArticles,
            'daily_average' => $dailyAverage,
            'total_articles' => $totalArticles
        ]);
        
        $this->info('✅ Weekly report generated successfully!');
        
        return 0;
    }
}
