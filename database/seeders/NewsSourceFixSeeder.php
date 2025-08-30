<?php

namespace Database\Seeders;

use App\Models\NewsSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSourceFixSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('🔧 Fixing existing news sources...');

        // Update all news sources to ensure they have proper slugs and are active
        $sources = NewsSource::all();
        
        $updated = 0;
        
        foreach ($sources as $source) {
            $needsUpdate = false;
            $updates = [];
            
            // Generate slug if missing
            if (empty($source->slug)) {
                $updates['slug'] = Str::slug($source->name);
                $needsUpdate = true;
            }
            
            // Activate if not active
            if (!$source->is_active) {
                $updates['is_active'] = true;
                $needsUpdate = true;
            }
            
            // Set next_scrape_at if null
            if (is_null($source->next_scrape_at)) {
                $updates['next_scrape_at'] = now();
                $needsUpdate = true;
            }
            
            // Set auto_scraping_enabled if not set for gaming sources
            if ($source->is_gaming_source && !$source->auto_scraping_enabled) {
                $updates['auto_scraping_enabled'] = true;
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $source->update($updates);
                $slug = isset($updates['slug']) ? $updates['slug'] : $source->slug;
                $this->command->line("  ✅ Updated: {$source->name} -> slug: {$slug}");
                $updated++;
            }
        }
        
        if ($updated > 0) {
            $this->command->info("🎉 Fixed {$updated} news source(s)!");
        } else {
            $this->command->info("✅ All news sources are already properly configured!");
        }
        
        // Show summary of gaming sources
        $gamingSources = NewsSource::gaming()->active()->get(['name', 'slug', 'trust_score']);
        
        $this->command->info('📊 Active Gaming News Sources:');
        foreach ($gamingSources as $source) {
            $this->command->line("  • {$source->name} (slug: {$source->slug}, trust: {$source->trust_score})");
        }
    }
}
