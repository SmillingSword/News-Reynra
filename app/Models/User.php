<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'bio',
        'avatar',
        'gaming_preferences',
        'favorite_games',
        'timezone',
        'email_notifications',
        'push_notifications',
        'notification_settings',
        'last_active_at',
        'oauth_provider',
        'oauth_id',
        'is_verified',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'oauth_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gaming_preferences' => 'array',
            'favorite_games' => 'array',
            'notification_settings' => 'array',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'last_active_at' => 'datetime',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id'; // Temporary: use id instead of username
    }

    /**
     * Get the author profile for the user.
     */
    public function author(): HasOne
    {
        return $this->hasOne(Author::class);
    }

    /**
     * Get the user's comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the user's reactions.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get the user's bookmarks.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the user's articles through author relationship.
     */
    public function articles(): HasManyThrough
    {
        return $this->hasManyThrough(Article::class, Author::class);
    }

    /**
     * Check if the user is an author.
     */
    public function getIsAuthorAttribute(): bool
    {
        return $this->author()->exists();
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username ?? $this->name;
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate Gravatar URL as fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=150";
    }

    /**
     * Check if the user has verified their email.
     */
    public function getIsEmailVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Check if the user is active (has been active recently).
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->last_active_at && $this->last_active_at->gt(now()->subDays(30));
    }

    /**
     * Update the user's last active timestamp.
     */
    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Get formatted gaming preferences.
     */
    public function getFormattedGamingPreferencesAttribute(): string
    {
        if (!$this->gaming_preferences) {
            return '';
        }

        return collect($this->gaming_preferences)->join(', ');
    }

    /**
     * Get formatted favorite games.
     */
    public function getFormattedFavoriteGamesAttribute(): string
    {
        if (!$this->favorite_games) {
            return '';
        }

        return collect($this->favorite_games)->join(', ');
    }
}
