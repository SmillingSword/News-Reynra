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
        Schema::create('scraping_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_source_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->enum('type', ['manual', 'scheduled', 'retry'])->default('scheduled');
            
            // Job details
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            
            // Results
            $table->integer('articles_found')->default(0);
            $table->integer('articles_processed')->default(0);
            $table->integer('articles_created')->default(0);
            $table->integer('articles_updated')->default(0);
            $table->integer('articles_skipped')->default(0);
            
            // Error handling
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            
            // Metadata
            $table->json('scraping_metadata')->nullable(); // URLs scraped, filters applied, etc.
            $table->json('performance_metrics')->nullable(); // Response times, memory usage, etc.
            
            $table->timestamps();
            
            $table->index(['news_source_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('next_retry_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraping_jobs');
    }
};
