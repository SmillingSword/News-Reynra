<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleView extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the article that was viewed.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user who viewed the article (if logged in).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a view for an article.
     */
    public static function recordView(Article $article, $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $referrer = $request->header('referer');
        $userId = auth()->id();
        
        // Check if this IP has viewed this article in the last hour to prevent spam
        $recentView = static::where('article_id', $article->id)
            ->where('ip_address', $ipAddress)
            ->where('viewed_at', '>', now()->subHour())
            ->first();

        if (!$recentView) {
            static::create([
                'article_id' => $article->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
                'viewed_at' => now(),
            ]);

            // Update article views count cache
            $article->increment('views_count');
        }
    }

    /**
     * Get unique views count for an article.
     */
    public static function getUniqueViewsCount($articleId)
    {
        return static::where('article_id', $articleId)
            ->distinct('ip_address')
            ->count();
    }

    /**
     * Get views count by date range.
     */
    public static function getViewsByDateRange($articleId, $startDate, $endDate)
    {
        return static::where('article_id', $articleId)
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get top referrers for an article.
     */
    public static function getTopReferrers($articleId, $limit = 10)
    {
        return static::where('article_id', $articleId)
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as views_count')
            ->groupBy('referrer')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();
    }
}
