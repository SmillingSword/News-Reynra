<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class NewsSource extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'url',
        'description',
        'logo',
        'trust_score',
        'type',
        'is_active',
        'contact_info',
        'scraping_config',
        'rss_url',
        'scraping_frequency',
        'last_scraped_at',
        'next_scrape_at',
        'auto_scraping_enabled',
        'content_filters',
        'keyword_filters',
        'total_scraped',
        'successful_scrapes',
        'failed_scrapes',
        'is_gaming_source',
        'gaming_categories',
    ];

    protected $casts = [
        'trust_score' => 'integer',
        'is_active' => 'boolean',
        'contact_info' => 'array',
        'scraping_config' => 'array',
        'scraping_frequency' => 'integer',
        'last_scraped_at' => 'datetime',
        'next_scrape_at' => 'datetime',
        'auto_scraping_enabled' => 'boolean',
        'content_filters' => 'array',
        'keyword_filters' => 'array',
        'total_scraped' => 'integer',
        'successful_scrapes' => 'integer',
        'failed_scrapes' => 'integer',
        'is_gaming_source' => 'boolean',
        'gaming_categories' => 'array',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
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
     * Get all articles from this news source.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get all scraping jobs for this news source.
     */
    public function scrapingJobs(): HasMany
    {
        return $this->hasMany(ScrapingJob::class);
    }

    /**
     * Scope to get only active news sources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get news sources by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get trusted news sources (high trust score).
     */
    public function scopeTrusted($query, $minScore = 7)
    {
        return $query->where('trust_score', '>=', $minScore);
    }

    /**
     * Scope to get gaming news sources.
     */
    public function scopeGaming($query)
    {
        return $query->where('is_gaming_source', true);
    }

    /**
     * Scope to get sources ready for scraping.
     */
    public function scopeReadyForScraping($query)
    {
        return $query->where('is_active', true)
            ->where('auto_scraping_enabled', true)
            ->where(function ($q) {
                $q->whereNull('next_scrape_at')
                  ->orWhere('next_scrape_at', '<=', now());
            });
    }

    /**
     * Get available source types.
     */
    public static function getTypes(): array
    {
        return [
            'official' => 'Official',
            'media' => 'Media Outlet',
            'community' => 'Community',
            'social' => 'Social Media',
        ];
    }

    /**
     * Get the trust level based on score.
     */
    public function getTrustLevelAttribute(): string
    {
        return match (true) {
            $this->trust_score >= 9 => 'Excellent',
            $this->trust_score >= 7 => 'High',
            $this->trust_score >= 5 => 'Medium',
            $this->trust_score >= 3 => 'Low',
            default => 'Very Low',
        };
    }

    /**
     * Get the trust level color for UI.
     */
    public function getTrustColorAttribute(): string
    {
        return match (true) {
            $this->trust_score >= 9 => 'green',
            $this->trust_score >= 7 => 'blue',
            $this->trust_score >= 5 => 'yellow',
            $this->trust_score >= 3 => 'orange',
            default => 'red',
        };
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Update scraping statistics.
     */
    public function updateScrapingStats(bool $success): void
    {
        $this->increment('total_scraped');
        
        if ($success) {
            $this->increment('successful_scrapes');
        } else {
            $this->increment('failed_scrapes');
        }

        $this->update([
            'last_scraped_at' => now(),
            'next_scrape_at' => now()->addMinutes($this->scraping_frequency),
        ]);
    }

    /**
     * Get scraping success rate.
     */
    public function getScrapingSuccessRateAttribute(): float
    {
        if ($this->total_scraped === 0) {
            return 0;
        }

        return round(($this->successful_scrapes / $this->total_scraped) * 100, 2);
    }

    /**
     * Check if source is ready for scraping.
     */
    public function isReadyForScraping(): bool
    {
        return $this->is_active && 
               $this->auto_scraping_enabled && 
               ($this->next_scrape_at === null || $this->next_scrape_at <= now());
    }

    /**
     * Get default scraping configuration for gaming sources.
     */
    public static function getDefaultGamingScrapingConfig(): array
    {
        return [
            'selectors' => [
                'article_links' => 'a[href*="/news/"], a[href*="/artikel/"], .post-title a',
                'title' => 'h1, .entry-title, .post-title, .article-title',
                'content' => '.entry-content, .post-content, .article-content, .content',
                'excerpt' => '.excerpt, .summary, .lead',
                'image' => '.featured-image img, .post-thumbnail img, .article-image img',
                'date' => '.post-date, .article-date, .published, time',
                'author' => '.author, .byline, .post-author',
            ],
            'filters' => [
                'min_content_length' => 200,
                'exclude_patterns' => ['iklan', 'advertisement', 'sponsored'],
                'required_keywords' => ['game', 'gaming', 'esport', 'mobile legends', 'pubg', 'valorant'],
            ],
            'limits' => [
                'max_articles_per_scrape' => 20,
                'request_delay' => 2, // seconds between requests
            ],
        ];
    }

    /**
     * Get gaming categories for Indonesian gaming sites.
     */
    public static function getGamingCategories(): array
    {
        return [
            'mobile_games' => 'Mobile Games',
            'pc_games' => 'PC Games',
            'console_games' => 'Console Games',
            'esports' => 'Esports',
            'tournaments' => 'Tournaments',
            'reviews' => 'Game Reviews',
            'news' => 'Gaming News',
            'tips_tricks' => 'Tips & Tricks',
            'industry' => 'Gaming Industry',
            'streaming' => 'Game Streaming',
        ];
    }
}
