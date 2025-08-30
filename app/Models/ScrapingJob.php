<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapingJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_source_id',
        'status',
        'type',
        'started_at',
        'completed_at',
        'duration_seconds',
        'articles_found',
        'articles_processed',
        'articles_created',
        'articles_updated',
        'articles_skipped',
        'error_message',
        'error_details',
        'retry_count',
        'next_retry_at',
        'scraping_metadata',
        'performance_metrics',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'next_retry_at' => 'datetime',
        'error_details' => 'array',
        'scraping_metadata' => 'array',
        'performance_metrics' => 'array',
        'articles_found' => 'integer',
        'articles_processed' => 'integer',
        'articles_created' => 'integer',
        'articles_updated' => 'integer',
        'articles_skipped' => 'integer',
        'duration_seconds' => 'integer',
        'retry_count' => 'integer',
    ];

    /**
     * Get the news source for this scraping job.
     */
    public function newsSource(): BelongsTo
    {
        return $this->belongsTo(NewsSource::class);
    }

    /**
     * Scope to get only pending jobs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only running jobs.
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope to get only completed jobs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only failed jobs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get jobs that need retry.
     */
    public function scopeNeedsRetry($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where(function ($q) {
                $q->whereNull('next_retry_at')
                  ->orWhere('next_retry_at', '<=', now());
            });
    }

    /**
     * Scope to get recent jobs.
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Mark job as started.
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark job as completed.
     */
    public function markAsCompleted(array $results = []): void
    {
        $this->update(array_merge([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
        ], $results));
    }

    /**
     * Mark job as failed.
     */
    public function markAsFailed(string $errorMessage, array $errorDetails = []): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? now()->diffInSeconds($this->started_at) : null,
            'error_message' => $errorMessage,
            'error_details' => $errorDetails,
            'retry_count' => $this->retry_count + 1,
            'next_retry_at' => $this->calculateNextRetryTime(),
        ]);
    }

    /**
     * Calculate next retry time based on retry count.
     */
    private function calculateNextRetryTime(): ?\Carbon\Carbon
    {
        if ($this->retry_count >= 3) {
            return null; // No more retries
        }

        // Exponential backoff: 5 minutes, 15 minutes, 45 minutes
        $delays = [5, 15, 45];
        $delayMinutes = $delays[$this->retry_count] ?? 45;

        return now()->addMinutes($delayMinutes);
    }

    /**
     * Get success rate as percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->articles_found === 0) {
            return 0;
        }

        return round(($this->articles_processed / $this->articles_found) * 100, 2);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    /**
     * Check if job can be retried.
     */
    public function canRetry(): bool
    {
        return $this->status === 'failed' && 
               $this->retry_count < 3 && 
               ($this->next_retry_at === null || $this->next_retry_at <= now());
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'gray',
            'running' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status icon for UI.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'clock',
            'running' => 'play',
            'completed' => 'check',
            'failed' => 'x',
            default => 'question',
        };
    }
}
