<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class ShowLatestArticleCommand extends Command
{
    protected $signature = 'articles:show-latest';
    protected $description = 'Show the latest scraped article content';

    public function handle(): int
    {
        $article = Article::latest()->first();
        
        if (!$article) {
            $this->error('No articles found');
            return self::FAILURE;
        }
        
        $this->info('🎯 LATEST ARTICLE PREVIEW:');
        $this->newLine();
        
        $this->line('📰 TITLE: ' . $article->title);
        $this->newLine();
        
        $this->line('📝 EXCERPT: ' . $article->excerpt);
        $this->newLine();
        
        $this->line('📄 CONTENT (first 800 characters):');
        $this->line(substr($article->content, 0, 800) . '...');
        $this->newLine();
        
        $this->line('📊 STATS:');
        $this->line('- Word Count: ' . str_word_count($article->content));
        $this->line('- Character Count: ' . strlen($article->content));
        $this->line('- Status: ' . $article->status);
        $this->line('- Published: ' . ($article->published_at ? $article->published_at->format('Y-m-d H:i:s') : 'Not published'));
        
        if (isset($article->meta['rewritten_by'])) {
            $this->line('- Rewritten by: ' . $article->meta['rewritten_by']);
        }
        
        return self::SUCCESS;
    }
}
