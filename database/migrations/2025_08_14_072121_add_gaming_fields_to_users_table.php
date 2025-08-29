<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->text('bio')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('bio');
            $table->json('gaming_preferences')->nullable()->after('avatar'); // Preferred platforms, genres
            $table->json('favorite_games')->nullable()->after('gaming_preferences');
            $table->string('timezone')->default('UTC')->after('favorite_games');
            $table->boolean('email_notifications')->default(true)->after('timezone');
            $table->boolean('push_notifications')->default(false)->after('email_notifications');
            $table->json('notification_settings')->nullable()->after('push_notifications'); // Granular notification preferences
            $table->timestamp('last_active_at')->nullable()->after('notification_settings');
            $table->string('oauth_provider')->nullable()->after('last_active_at'); // google, discord, etc.
            $table->string('oauth_id')->nullable()->after('oauth_provider');
            $table->boolean('is_verified')->default(false)->after('oauth_id');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            
            $table->index('username');
            $table->index(['oauth_provider', 'oauth_id']);
            $table->index('last_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropIndex(['oauth_provider', 'oauth_id']);
            $table->dropIndex(['last_active_at']);
            
            $table->dropColumn([
                'username',
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
                'verified_at'
            ]);
        });
    }
};
