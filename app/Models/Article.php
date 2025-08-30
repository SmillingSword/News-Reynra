<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
// use Laravel\Scout\Searchable; // Temporarily disabled until Scout is properly configured
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia; // Removed Searchable temporarily

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'content',
        'author_id',
        'news_source_id',
        'editor_id',
        'type',
        'status',
        'featured_image',
        'gallery',
        'reading_time',
        'views_count',
        'likes_count',
        'comments_count',
        'shares_count',
        'is_featured',
        'is_sponsored',
        'is_breaking',
        'allow_comments',
        'rating',
        'pros',
        'cons',
        'published_at',
        'scheduled_at',
        'meta',
        'social_meta',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_sponsored' => 'boolean',
        'is_breaking' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'reading_time' => 'integer',
        'rating' => 'decimal:1',
        'gallery' => 'array',
        'pros' => 'array',
        'cons' => 'array',
        'meta' => 'array',
        'social_meta' => 'array',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'excerpt' => $this->excerpt,
            'content' => strip_tags($this->content),
            'type' => $this->type,
            'status' => $this->status,
            'published_at' => $this->published_at?->timestamp,
            'categories' => $this->categories->pluck('name')->toArray(),
            'tags' => $this->tags->pluck('name')->toArray(),
            'games' => $this->games->pluck('title')->toArray(),
            'author' => $this->author->display_name ?? '',
        ];
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('attachments');
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(200)
            ->sharpen(10);

        $this->addMediaConversion('card')
            ->width(600)
            ->height(400)
            ->sharpen(10);

        $this->addMediaConversion('hero')
            ->width(1200)
            ->height(630)
            ->sharpen(10);
    }

    /**
     * Get the author of the article.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the news source of the article.
     */
    public function newsSource(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class);
    }

    /**
     * Get the categories for the article.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }

    /**
     * Get the tags for the article.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    /**
     * Get the games related to the article.
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'article_game');
    }

    /**
     * Get the comments for the article.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the reactions for the article.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Get the bookmarks for the article.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the views for the article.
     */
    public function views(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    /**
     * Scope to get only published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to get only draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get only featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get only breaking news.
     */
    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    /**
     * Scope to get articles by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get recent articles.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get popular articles (by views).
     */
    public function scopePopular($query, $days = 30)
    {
        return $query->where('published_at', '>=', now()->subDays($days))
            ->orderBy('views_count', 'desc');
    }

    /**
     * Get the article's featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured', 'hero');
    }

    /**
     * Get the article's card image URL.
     */
    public function getCardImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured', 'card');
    }

    /**
     * Get the article's thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured', 'thumb');
    }

    /**
     * Get the article's reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        if ($this->attributes['reading_time']) {
            return $this->attributes['reading_time'];
        }

        // Calculate reading time based on content (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Get formatted published date.
     */
    public function getFormattedPublishedDateAttribute(): string
    {
        return $this->published_at?->format('M j, Y') ?? '';
    }

    /**
     * Get the article's URL.
     */
    public function getUrlAttribute(): string
    {
        return route('articles.show', $this->slug);
    }

    /**
     * Check if the article is published.
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at <= now();
    }

    /**
     * Check if the article is scheduled.
     */
    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'published' && 
               $this->published_at && 
               $this->published_at > now();
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Record a view for this article.
     */
    public function recordView($ipAddress = null, $userId = null, $referrer = null, $userAgent = null): void
    {
        $request = request();
        
        ArticleView::create([
            'article_id' => $this->id,
            'user_id' => $userId ?: auth()->id(),
            'ip_address' => $ipAddress ?: $request->ip(),
            'user_agent' => $userAgent ?: $request->userAgent(),
            'referrer' => $referrer ?: $request->header('referer'),
            'viewed_at' => now(),
        ]);

        // Update the article's views count
        $this->increment('views_count');
    }

    /**
     * Get unique views count for this article.
     */
    public function getUniqueViewsCount(): int
    {
        return $this->views()->distinct('ip_address')->count();
    }

    /**
     * Get views count for a specific date range.
     */
    public function getViewsInDateRange($startDate, $endDate): int
    {
        return $this->views()
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->count();
    }

    /**
     * Get unique views count for a specific date range.
     */
    public function getUniqueViewsInDateRange($startDate, $endDate): int
    {
        return $this->views()
            ->whereBetween('viewed_at', [$startDate, $endDate])
            ->distinct('ip_address')
            ->count();
    }

    /**
     * Get today's views count.
     */
    public function getTodayViewsCount(): int
    {
        return $this->getViewsInDateRange(
            now()->startOfDay(),
            now()->endOfDay()
        );
    }

    /**
     * Get this week's views count.
     */
    public function getWeekViewsCount(): int
    {
        return $this->getViewsInDateRange(
            now()->startOfWeek(),
            now()->endOfWeek()
        );
    }

    /**
     * Get this month's views count.
     */
    public function getMonthViewsCount(): int
    {
        return $this->getViewsInDateRange(
            now()->startOfMonth(),
            now()->endOfMonth()
        );
    }
}
