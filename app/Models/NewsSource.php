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
    ];

    protected $casts = [
        'trust_score' => 'integer',
        'is_active' => 'boolean',
        'contact_info' => 'array',
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
}
