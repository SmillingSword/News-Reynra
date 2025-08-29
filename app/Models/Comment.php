<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
        'author_name',
        'author_email',
        'author_ip',
        'status',
        'likes_count',
        'replies_count',
        'is_pinned',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'approved_at' => 'datetime',
        'likes_count' => 'integer',
        'replies_count' => 'integer',
    ];

    /**
     * Get the article that owns the comment.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', 'approved')
            ->orderBy('created_at');
    }

    /**
     * Get the user who approved the comment.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the reactions for the comment.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only pending comments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get pinned comments.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Get the comment author's name.
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->attributes['author_name'] ?? 'Anonymous';
    }

    /**
     * Get the comment author's avatar.
     */
    public function getAuthorAvatarAttribute(): ?string
    {
        return $this->user?->avatar ?? null;
    }

    /**
     * Check if the comment is approved.
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the comment is by a registered user.
     */
    public function getIsFromUserAttribute(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Approve the comment.
     */
    public function approve(User $approver = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver?->id,
        ]);
    }

    /**
     * Reject the comment.
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Mark as spam.
     */
    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }
}
