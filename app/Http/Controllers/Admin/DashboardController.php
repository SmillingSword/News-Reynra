<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Game;
use App\Models\Tag;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Bookmark;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Temporary: Allow any authenticated user to access admin (we'll fix permissions later)
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Get comprehensive dashboard statistics
        $stats = [
            'articles' => [
                'total' => Article::count(),
                'published' => Article::where('status', 'published')->count(),
                'draft' => Article::where('status', 'draft')->count(),
                'pending_review' => Article::where('status', 'pending_review')->count(),
                'this_month' => Article::whereMonth('created_at', now()->month)->count(),
                'last_month' => Article::whereMonth('created_at', now()->subMonth()->month)->count(),
                'growth_rate' => $this->calculateGrowthRate('articles'),
            ],
            'users' => [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'active_today' => User::whereDate('last_login_at', now())->count(),
                'growth_rate' => $this->calculateGrowthRate('users'),
            ],
            'content' => [
                'categories' => Category::count(),
                'tags' => Tag::count(),
                'games' => Game::count(),
                'comments' => Comment::count(),
                'reactions' => Reaction::count(),
                'bookmarks' => Bookmark::count(),
            ],
            'engagement' => [
                'total_views' => Article::sum('views_count') ?? 0,
                'avg_views_per_article' => Article::where('views_count', '>', 0)->avg('views_count') ?? 0,
                'most_viewed_article' => Article::orderBy('views_count', 'desc')->first(),
                'engagement_rate' => $this->calculateEngagementRate(),
            ]
        ];

        // Get recent articles with full details
        $recentArticles = Article::with(['author', 'categories', 'tags'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'status' => $article->status,
                    'views_count' => $article->views_count ?? 0,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                    'author' => [
                        'name' => $article->author ? $article->author->name : 'Unknown Author',
                        'avatar' => $article->author ? $article->author->avatar_url : null,
                    ],
                    'categories' => $article->categories->map(function ($category) {
                        return [
                            'name' => $category->name,
                            'color' => $category->color ?? '#6366f1',
                        ];
                    }),
                    'tags' => $article->tags->pluck('name'),
                ];
            });

        // Get recent comments with user details
        $recentComments = Comment::with(['user', 'article'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'name' => $comment->user ? $comment->user->name : 'Anonymous',
                        'avatar' => $comment->user ? $comment->user->avatar_url : null,
                    ],
                    'article' => [
                        'id' => $comment->article ? $comment->article->id : null,
                        'title' => $comment->article ? $comment->article->title : 'Unknown Article',
                        'slug' => $comment->article ? $comment->article->slug : null,
                    ]
                ];
            });

        // Get popular categories with article counts
        $popularCategories = Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'color' => $category->color ?? '#6366f1',
                    'articles_count' => $category->articles_count,
                    'description' => $category->description,
                ];
            });

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get chart data for the last 30 days
        $chartData = $this->getChartData();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentArticles' => $recentArticles,
            'recentComments' => $recentComments,
            'popularCategories' => $popularCategories,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get dashboard analytics data.
     */
    public function analytics()
    {
        // Temporary: Allow any authenticated user to access admin
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Article publishing trends (last 30 days)
        $articleTrends = Article::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User registration trends (last 30 days)
        $userTrends = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Most popular tags
        $popularTags = Tag::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(10)
            ->get();

        // Category performance
        $categoryPerformance = Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(10)
            ->get();

        // Engagement metrics
        $engagementMetrics = [
            'total_views' => Article::sum('views_count') ?? 0,
            'total_reactions' => Reaction::count(),
            'total_bookmarks' => Bookmark::count(),
            'total_comments' => Comment::count(),
        ];

        return response()->json([
            'articleTrends' => $articleTrends,
            'userTrends' => $userTrends,
            'popularTags' => $popularTags,
            'categoryPerformance' => $categoryPerformance,
            'engagementMetrics' => $engagementMetrics,
        ]);
    }

    /**
     * Get chart data for the dashboard
     */
    private function getChartData()
    {
        // Get data for the last 30 days
        $startDate = now()->subDays(30);
        
        // Articles published per day
        $articlesPerDay = Article::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Users registered per day
        $usersPerDay = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Comments created per day
        $commentsPerDay = Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        // Generate labels for the last 30 days
        $labels = collect();
        $articleData = collect();
        $userData = collect();
        $commentData = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels->push(now()->subDays($i)->format('M j'));
            
            $articleData->push($articlesPerDay[$date] ?? 0);
            $userData->push($usersPerDay[$date] ?? 0);
            $commentData->push($commentsPerDay[$date] ?? 0);
        }

        return [
            'labels' => $labels->toArray(),
            'datasets' => [
                [
                    'label' => 'Articles Published',
                    'data' => $articleData->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Users Registered',
                    'data' => $userData->toArray(),
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Comments Created',
                    'data' => $commentData->toArray(),
                    'borderColor' => 'rgb(251, 146, 60)',
                    'backgroundColor' => 'rgba(251, 146, 60, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        return collect()
            ->merge(
                Article::with('author')
                    ->latest()
                    ->limit(3)
                    ->get()
                    ->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'type' => 'article.created',
                            'description' => 'New article "' . $article->title . '" was ' . $article->status,
                            'user' => [
                                'name' => $article->author ? $article->author->name : 'System',
                            ],
                            'created_at' => $article->created_at,
                            'url' => route('admin.articles.show', $article->id),
                        ];
                    })
            )
            ->merge(
                Comment::with(['user', 'article'])
                    ->latest()
                    ->limit(3)
                    ->get()
                    ->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'type' => 'comment.created',
                            'description' => 'New comment on "' . ($comment->article ? $comment->article->title : 'Unknown Article') . '"',
                            'user' => [
                                'name' => $comment->user ? $comment->user->name : 'Anonymous',
                            ],
                            'created_at' => $comment->created_at,
                            'url' => $comment->article ? route('admin.articles.show', $comment->article->id) : null,
                        ];
                    })
            )
            ->merge(
                User::latest()
                    ->limit(3)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'type' => 'user.registered',
                            'description' => 'New user registered: ' . $user->name,
                            'user' => [
                                'name' => $user->name,
                            ],
                            'created_at' => $user->created_at,
                            'url' => route('admin.users.show', $user->id),
                        ];
                    })
            )
            ->sortByDesc('created_at')
            ->take(5)
            ->values();
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate($type)
    {
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;
        
        switch ($type) {
            case 'articles':
                $current = Article::whereMonth('created_at', $currentMonth)->count();
                $previous = Article::whereMonth('created_at', $lastMonth)->count();
                break;
            case 'users':
                $current = User::whereMonth('created_at', $currentMonth)->count();
                $previous = User::whereMonth('created_at', $lastMonth)->count();
                break;
            default:
                return 0;
        }
        
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculate engagement rate
     */
    private function calculateEngagementRate()
    {
        $totalArticles = Article::count();
        if ($totalArticles == 0) {
            return 0;
        }
        
        $totalEngagements = Reaction::count() + Comment::count() + Bookmark::count();
        return round($totalEngagements / $totalArticles, 2);
    }
}
