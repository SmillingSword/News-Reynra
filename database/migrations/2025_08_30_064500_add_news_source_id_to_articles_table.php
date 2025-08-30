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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('news_source_id')->nullable()->after('author_id');
            $table->foreign('news_source_id')->references('id')->on('news_sources')->onDelete('set null');
            $table->index('news_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['news_source_id']);
            $table->dropIndex(['news_source_id']);
            $table->dropColumn('news_source_id');
        });
    }
};
