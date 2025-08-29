<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'folder',
        'notes',
    ];

    /**
     * Get the user that owns the bookmark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the article that is bookmarked.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Scope to get bookmarks in a specific folder.
     */
    public function scopeInFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }

    /**
     * Scope to get bookmarks without a folder.
     */
    public function scopeWithoutFolder($query)
    {
        return $query->whereNull('folder');
    }

    /**
     * Scope to get bookmarks by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get available folders for a user.
     */
    public static function getFoldersForUser($userId): array
    {
        return self::where('user_id', $userId)
            ->whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->toArray();
    }

    /**
     * Check if the bookmark has notes.
     */
    public function getHasNotesAttribute(): bool
    {
        return !empty($this->notes);
    }
}
