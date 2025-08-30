<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class CheckFeaturedImageCommand extends Command
{
    protected $signature = 'articles:check-images';
    protected $description = 'Check featured images of latest articles';

    public function handle(): int
    {
        $articles = Article::latest()->take(3)->get(['title', 'featured_image', 'status']);
        
        $this->info('🖼️  Latest Articles Featured Images:');
        $this->newLine();
        
        foreach ($articles as $article) {
            $this->line("📰 {$article->title}");
            $this->line("   Status: {$article->status}");
            $this->line("   Image: " . ($article->featured_image ?: 'No image'));
            $this->newLine();
        }
        
        return self::SUCCESS;
    }
}
