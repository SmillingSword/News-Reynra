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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('platforms')->nullable(); // ['PC', 'PS5', 'Xbox', 'Switch', 'Mobile']
            $table->json('genres')->nullable(); // ['Action', 'RPG', 'Strategy', etc.]
            $table->string('developer')->nullable();
            $table->string('publisher')->nullable();
            $table->date('release_date')->nullable();
            $table->string('esrb_rating')->nullable(); // E, T, M, etc.
            $table->decimal('metacritic_score', 3, 1)->nullable();
            $table->string('official_website')->nullable();
            $table->json('social_links')->nullable(); // Twitter, Discord, etc.
            $table->string('cover_image')->nullable();
            $table->json('screenshots')->nullable();
            $table->json('trailers')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable(); // SEO metadata
            $table->timestamps();
            
            $table->index(['is_active', 'is_featured']);
            $table->index('slug');
            $table->index('release_date');
            $table->index(['developer', 'publisher']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
