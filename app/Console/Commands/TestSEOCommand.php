<?php

namespace App\Console\Commands;

use App\Services\ContentRewriterService;
use Illuminate\Console\Command;

class TestSEOCommand extends Command
{
    protected $signature = 'seo:test {--title= : Article title to test}';
    protected $description = 'Test SEO metadata generation with hashtags';

    public function handle(): int
    {
        $title = $this->option('title') ?: 'Demo Resident Evil Requiem Rilis ke Publik sedang Dipertimbangkan oleh Capcom';
        $content = 'Berita gaming terbaru mengungkap kabar terbaru seputar Resident Evil yang lagi jadi perbincangan hangat. Developer game kece Capcom menghadirkan permainan epic yang bikin nagih para gamer Indonesia.';
        
        $service = new ContentRewriterService();
        $seo = $service->generateSEOMetadata($title, $content);
        
        $this->info('🎯 SEO METADATA PREVIEW:');
        $this->newLine();
        
        $this->line("📰 Meta Title:");
        $this->line($seo['meta_title']);
        $this->newLine();
        
        $this->line("📝 Meta Description:");
        $this->line($seo['meta_description']);
        $this->newLine();
        
        $this->line("🔍 Keywords:");
        $this->line($seo['keywords']);
        $this->newLine();
        
        $this->line("📱 Hashtags:");
        $this->line(implode(' ', $seo['hashtags']));
        $this->newLine();
        
        $this->line("📲 Social Title:");
        $this->line($seo['social_title']);
        $this->newLine();
        
        $this->line("💬 Social Description:");
        $this->line($seo['social_description']);
        $this->newLine();
        
        $this->info("🏷️ Open Graph Tags:");
        foreach ($seo['og_tags'] as $key => $value) {
            $this->line("- {$key}: {$value}");
        }
        $this->newLine();
        
        $this->info("🐦 Twitter Tags:");
        foreach ($seo['twitter_tags'] as $key => $value) {
            $this->line("- {$key}: {$value}");
        }
        
        return self::SUCCESS;
    }
}
