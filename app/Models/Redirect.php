<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_url',
        'new_url',
        'status_code',
        'hits',
        'is_active',
        'reason',
        'last_hit_at',
    ];

    protected $casts = [
        'hits' => 'integer',
        'status_code' => 'integer',
        'is_active' => 'boolean',
        'last_hit_at' => 'datetime',
    ];

    /**
     * Scope to get only active redirects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get redirects by status code.
     */
    public function scopeByStatusCode($query, $statusCode)
    {
        return $query->where('status_code', $statusCode);
    }

    /**
     * Scope to get popular redirects (most hits).
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('hits', 'desc')->limit($limit);
    }

    /**
     * Find a redirect by old URL.
     */
    public static function findByOldUrl($url): ?self
    {
        return self::active()->where('old_url', $url)->first();
    }

    /**
     * Record a hit for this redirect.
     */
    public function recordHit(): void
    {
        $this->increment('hits');
        $this->update(['last_hit_at' => now()]);
    }

    /**
     * Get available status codes.
     */
    public static function getStatusCodes(): array
    {
        return [
            301 => 'Permanent Redirect',
            302 => 'Temporary Redirect',
            307 => 'Temporary Redirect (Method Preserved)',
            308 => 'Permanent Redirect (Method Preserved)',
        ];
    }

    /**
     * Get the status code description.
     */
    public function getStatusDescriptionAttribute(): string
    {
        return self::getStatusCodes()[$this->status_code] ?? 'Unknown';
    }
}
