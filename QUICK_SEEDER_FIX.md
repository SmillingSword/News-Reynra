# 🚀 Quick Seeder Fix - Manual Run

## 🔍 Masalah yang Terjadi

Seeder `GamingNewsSourceSeeder` dan `NewsSourceFixSeeder` tidak berjalan saat `php artisan db:seed`.

## ✅ **Solusi Cepat - Manual Run:**

### Step 1: Jalankan Gaming News Source Seeder
```bash
php artisan db:seed --class=GamingNewsSourceSeeder
```

### Step 2: Jalankan News Source Fix Seeder  
```bash
php artisan db:seed --class=NewsSourceFixSeeder
```

### Step 3: Verifikasi Gaming Sources
```bash
php artisan tinker
```

```php
// Cek semua gaming sources
\App\Models\NewsSource::gaming()->active()->get(['name', 'slug', 'trust_score'])->toArray();

// Cek OneEsport spesifik
\App\Models\NewsSource::where('name', 'OneEsport')->first();

exit
```

### Step 4: Test OneEsport Scraping
```bash
php artisan news:scrape --source="oneesport" --force --sync
```

## 🔧 **Alternatif - Manual Database Update:**

Jika seeder tidak ada, gunakan tinker:

```bash
php artisan tinker
```

```php
// Create OneEsport manually
\App\Models\NewsSource::updateOrCreate(
    ['url' => 'https://www.oneesports.id'],
    [
        'name' => 'OneEsport',
        'slug' => 'oneesport',
        'description' => 'Platform esports terkemuka dengan berita, analisis, dan konten gaming terbaru.',
        'trust_score' => 8,
        'type' => 'media',
        'is_gaming_source' => true,
        'is_active' => true,
        'scraping_frequency' => 45,
        'auto_scraping_enabled' => true,
        'next_scrape_at' => now(),
        'gaming_categories' => ['esports', 'mobile_games', 'pc_games', 'news', 'tournaments'],
        'rss_url' => 'https://www.oneesports.id/feed/',
    ]
);

// Update all existing sources
\App\Models\NewsSource::whereNull('slug')->orWhere('is_active', false)->get()->each(function($source) {
    $source->update([
        'is_active' => true,
        'slug' => \Illuminate\Support\Str::slug($source->name),
        'next_scrape_at' => now()
    ]);
    echo "Updated: {$source->name} -> {$source->slug}\n";
});

exit
```

## 🎯 **Expected Results:**

Setelah fix, command ini harus berhasil:
```bash
php artisan news:scrape --source="oneesport" --force --sync
```

Output yang diharapkan:
```
🚀 Starting news scraping process...
Found 1 news source(s) to scrape:
  • OneEsport (https://www.oneesports.id)

📰 Starting scraping jobs...
🔄 Scraping OneEsport synchronously...

✅ Scraping process completed!
   Jobs dispatched: 1
   Jobs skipped: 0
```

## 🚀 **Test Auto Content Creator:**

```bash
php artisan news:auto-create-content --limit=10
```

Sekarang harus membuat 10 artikel (bukan 3).

## 📋 **Debug Commands:**

```bash
# Cek apakah seeder files ada
ls -la database/seeders/

# Cek semua news sources
php artisan tinker -c "\App\Models\NewsSource::all(['name', 'slug', 'is_active'])->toArray()"

# Test scraping semua gaming sources
php artisan news:scrape --gaming --dry-run
