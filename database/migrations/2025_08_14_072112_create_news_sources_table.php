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
        Schema::create('news_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->integer('trust_score')->default(5); // 1-10 scale
            $table->enum('type', ['official', 'media', 'community', 'social'])->default('media');
            $table->boolean('is_active')->default(true);
            $table->json('contact_info')->nullable(); // Email, social media, etc.
            $table->timestamps();
            
            $table->index(['is_active', 'trust_score']);
            $table->index('slug');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_sources');
    }
};
