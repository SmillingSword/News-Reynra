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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->json('specialties')->nullable(); // Gaming genres/platforms they specialize in
            $table->json('social_links')->nullable(); // Twitter, Twitch, YouTube, etc.
            $table->string('location')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('articles_count')->default(0);
            $table->timestamp('last_article_at')->nullable();
            $table->json('meta')->nullable(); // SEO metadata
            $table->timestamps();
            
            $table->index(['is_active', 'is_featured']);
            $table->index('slug');
            $table->index('articles_count');
            $table->index('last_article_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
