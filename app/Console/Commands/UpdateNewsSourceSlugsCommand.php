<?php

namespace App\Console\Commands;

use App\Models\NewsSource;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateNewsSourceSlugsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'news:update-slugs {--force : Force update all slugs}';

    /**
     * The console command description.
     */
    protected $description = 'Update missing slugs for news sources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Updating news source slugs...');

        $query = NewsSource::query();
        
        if (!$this->option('force')) {
            $query->whereNull('slug');
        }
        
        $sources = $query->get();

        if ($sources->isEmpty()) {
            $this->info('✅ All news sources already have slugs.');
            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($sources as $source) {
            $oldSlug = $source->slug;
            $newSlug = Str::slug($source->name);
            
            $source->update(['slug' => $newSlug]);
            
            $this->line("  • {$source->name}: '{$oldSlug}' → '{$newSlug}'");
            $updated++;
        }

        $this->info("✅ Updated {$updated} news source slug(s).");
        
        return self::SUCCESS;
    }
}
