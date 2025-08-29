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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('subtitle')->nullable();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('editor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->enum('type', ['news', 'review', 'opinion', 'guide', 'interview'])->default('news');
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable(); // Additional images
            $table->integer('reading_time')->nullable(); // In minutes
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->decimal('rating', 3, 1)->nullable(); // For reviews (1.0 - 10.0)
            $table->json('pros')->nullable(); // For reviews
            $table->json('cons')->nullable(); // For reviews
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->json('meta')->nullable(); // SEO metadata (title, description, keywords)
            $table->json('social_meta')->nullable(); // OpenGraph, Twitter Card data
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'published_at']);
            $table->index(['is_breaking', 'published_at']);
            $table->index(['type', 'status']);
            $table->index('slug');
            $table->index('views_count');
            $table->index('scheduled_at');
            // Note: fullText index removed for SQLite compatibility - will use Laravel Scout for search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
