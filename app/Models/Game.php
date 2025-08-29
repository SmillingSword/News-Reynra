<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Game extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'platforms',
        'genres',
        'developer',
        'publisher',
        'release_date',
        'esrb_rating',
        'metacritic_score',
        'official_website',
        'social_links',
        'cover_image',
        'screenshots',
        'trailers',
        'is_featured',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'platforms' => 'array',
        'genres' => 'array',
        'social_links' => 'array',
        'screenshots' => 'array',
        'trailers' => 'array',
        'meta' => 'array',
        'release_date' => 'date',
        'metacritic_score' => 'decimal:3,1',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
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
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('screenshots')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('logos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml']);
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('card')
            ->width(600)
            ->height(400)
            ->sharpen(10);
    }

    /**
     * Get all articles related to this game.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_game');
    }

    /**
     * Scope to get only active games.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by platform.
     */
    public function scopeForPlatform($query, $platform)
    {
        return $query->whereJsonContains('platforms', $platform);
    }

    /**
     * Scope to filter by genre.
     */
    public function scopeForGenre($query, $genre)
    {
        return $query->whereJsonContains('genres', $genre);
    }

    /**
     * Scope to get upcoming games.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('release_date', '>', now());
    }

    /**
     * Scope to get recently released games.
     */
    public function scopeRecentlyReleased($query, $days = 30)
    {
        return $query->whereBetween('release_date', [
            now()->subDays($days),
            now()
        ]);
    }

    /**
     * Get the game's cover image URL.
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('cover', 'card');
    }

    /**
     * Get formatted platforms list.
     */
    public function getFormattedPlatformsAttribute(): string
    {
        if (!$this->platforms) {
            return '';
        }

        return collect($this->platforms)->join(', ');
    }

    /**
     * Get formatted genres list.
     */
    public function getFormattedGenresAttribute(): string
    {
        if (!$this->genres) {
            return '';
        }

        return collect($this->genres)->join(', ');
    }
}
