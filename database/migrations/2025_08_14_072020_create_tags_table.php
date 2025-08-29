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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#10B981'); // Hex color for UI
            $table->integer('usage_count')->default(0); // Track how many articles use this tag
            $table->boolean('is_featured')->default(false); // Featured tags for homepage
            $table->json('meta')->nullable(); // SEO metadata
            $table->timestamps();
            
            $table->index(['is_featured', 'usage_count']);
            $table->index('slug');
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
