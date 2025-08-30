<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\NewsSource;

class UpdateArticleSourcesCommand extends Command
{
    protected $signature = 'articles:update-sources';
    protected $description = 'Update existing articles with news source IDs';

    public function handle()
    {
        $this->info('🔄 Updating articles with news source IDs...');
        
        $articles = Article::whereNull('news_source_id')->get();
        $updated = 0;
        
        // Get GameBrott source
        $gameBrottSource = NewsSource::where('name', 'GameBrott')->first();
        
        foreach ($articles as $article) {
            // Assign all existing articles to GameBrott for now
            if ($gameBrottSource) {
                $article->update(['news_source_id' => $gameBrottSource->id]);
                $updated++;
            }
        }
        
        $this->info("✅ Updated {$updated} articles with source IDs");
        
        return 0;
    }
}
