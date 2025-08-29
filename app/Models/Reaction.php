<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reactable_type',
        'reactable_id',
        'user_id',
        'user_ip',
        'type',
    ];

    /**
     * Get the parent reactable model (article, comment, etc.).
     */
    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the reaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get reactions by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get reactions by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get reactions by IP (for guest users).
     */
    public function scopeByIp($query, $ip)
    {
        return $query->where('user_ip', $ip);
    }

    /**
     * Get available reaction types.
     */
    public static function getTypes(): array
    {
        return [
            'like' => '👍',
            'love' => '❤️',
            'laugh' => '😂',
            'angry' => '😠',
            'sad' => '😢',
            'wow' => '😮',
        ];
    }

    /**
     * Get the emoji for the reaction type.
     */
    public function getEmojiAttribute(): string
    {
        return self::getTypes()[$this->type] ?? '👍';
    }

    /**
     * Check if the reaction is from a registered user.
     */
    public function getIsFromUserAttribute(): bool
    {
        return !is_null($this->user_id);
    }
}
