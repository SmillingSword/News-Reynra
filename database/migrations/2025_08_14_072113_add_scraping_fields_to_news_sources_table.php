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
        Schema::table('news_sources', function (Blueprint $table) {
            // Scraping configuration
            $table->json('scraping_config')->nullable()->after('contact_info');
            $table->string('rss_url')->nullable()->after('scraping_config');
            $table->integer('scraping_frequency')->default(60)->after('rss_url'); // minutes
            $table->timestamp('last_scraped_at')->nullable()->after('scraping_frequency');
            $table->timestamp('next_scrape_at')->nullable()->after('last_scraped_at');
            $table->boolean('auto_scraping_enabled')->default(true)->after('next_scrape_at');
            
            // Content filtering
            $table->json('content_filters')->nullable()->after('auto_scraping_enabled');
            $table->json('keyword_filters')->nullable()->after('content_filters');
            
            // Statistics
            $table->integer('total_scraped')->default(0)->after('keyword_filters');
            $table->integer('successful_scrapes')->default(0)->after('total_scraped');
            $table->integer('failed_scrapes')->default(0)->after('successful_scrapes');
            
            // Gaming specific
            $table->boolean('is_gaming_source')->default(false)->after('failed_scrapes');
            $table->json('gaming_categories')->nullable()->after('is_gaming_source');
            
            $table->index(['auto_scraping_enabled', 'next_scrape_at']);
            $table->index('is_gaming_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_sources', function (Blueprint $table) {
            $table->dropColumn([
                'scraping_config',
                'rss_url',
                'scraping_frequency',
                'last_scraped_at',
                'next_scrape_at',
                'auto_scraping_enabled',
                'content_filters',
                'keyword_filters',
                'total_scraped',
                'successful_scrapes',
                'failed_scrapes',
                'is_gaming_source',
                'gaming_categories'
            ]);
        });
    }
};
