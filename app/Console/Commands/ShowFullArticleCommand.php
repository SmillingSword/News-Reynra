<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class ShowFullArticleCommand extends Command
{
    protected $signature = 'articles:show-full {--id= : Article ID to show}';
    protected $description = 'Show full article content with detailed stats';

    public function handle(): int
    {
        $articleId = $this->option('id');
        
        if ($articleId) {
            $article = Article::find($articleId);
        } else {
            $article = Article::latest()->first();
        }
        
        if (!$article) {
            $this->error('❌ No article found');
            return self::FAILURE;
        }
        
        $this->info('🎯 FULL ARTICLE PREVIEW:');
        $this->newLine();
        
        $this->line("📰 TITLE: {$article->title}");
        $this->newLine();
        
        $this->line("📝 EXCERPT:");
        $this->line($article->excerpt);
        $this->newLine();
        
        $this->line("🖼️  FEATURED IMAGE:");
        $this->line($article->featured_image ?: 'No image');
        $this->newLine();
        
        $this->line("📄 FULL CONTENT:");
        $this->line("=" . str_repeat("=", 80));
        $this->line($article->content);
        $this->line("=" . str_repeat("=", 80));
        $this->newLine();
        
        // Stats
        $wordCount = str_word_count(strip_tags($article->content));
        $charCount = strlen($article->content);
        $htmlTags = substr_count($article->content, '<');
        
        $this->info("📊 DETAILED STATS:");
        $this->line("- Word Count: {$wordCount}");
        $this->line("- Character Count: {$charCount}");
        $this->line("- HTML Tags: {$htmlTags}");
        $this->line("- Status: {$article->status}");
        $this->line("- Published: {$article->published_at}");
        $authorName = $article->author ? $article->author->display_name : 'Unknown';
        $this->line("- Author: {$authorName}");
        
        if ($article->meta) {
            $this->newLine();
            $this->line("🔧 META INFO:");
            foreach ($article->meta as $key => $value) {
                $this->line("- {$key}: {$value}");
            }
        }
        
        return self::SUCCESS;
    }
}
