# 🚀 OneEsport Quick Fix - Server Commands

## 🔧 Solusi Cepat Tanpa Command Baru

Karena command `news:update-slugs` belum tersedia di server, gunakan cara manual ini:

### Step 1: Fix via Tinker
```bash
php artisan tinker
```

### Step 2: Update OneEsport Source
```php
// Cari OneEsport source
$oneEsport = \App\Models\NewsSource::where('name', 'OneEsport')->first();

// Jika tidak ada, cek semua sources
\App\Models\NewsSource::pluck('name', 'id');

// Update OneEsport dengan field yang hilang
if ($oneEsport) {
    $oneEsport->update([
        'is_active' => true,
        'slug' => 'oneesport',
        'next_scrape_at' => now()
    ]);
    echo "OneEsport updated successfully!";
} else {
    echo "OneEsport not found. Running seeder...";
}

// Keluar dari tinker
exit
```

### Step 3: Re-run Seeder (Jika OneEsport Tidak Ada)
```bash
php artisan db:seed --class=GamingNewsSourceSeeder
```

### Step 4: Update Semua Sources via Tinker
```bash
php artisan tinker
```

```php
// Update semua sources yang tidak punya slug atau tidak aktif
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

### Step 5: Test OneEsport Scraping
```bash
# Test dengan slug
php artisan news:scrape --source="oneesport" --force --sync

# Atau test dengan nama (case sensitive)
php artisan news:scrape --source="OneEsport" --force --sync
```

### Step 6: Verifikasi Hasil
```bash
php artisan tinker
```

```php
// Cek OneEsport source
$oneEsport = \App\Models\NewsSource::where('slug', 'oneesport')->first();
echo "Name: {$oneEsport->name}\n";
echo "Slug: {$oneEsport->slug}\n";
echo "Active: " . ($oneEsport->is_active ? 'Yes' : 'No') . "\n";
echo "Trust Score: {$oneEsport->trust_score}\n";

// Cek semua gaming sources
\App\Models\NewsSource::gaming()->active()->pluck('name', 'slug');

exit
```

## 🎯 Expected Results

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

## 🔍 Debug Commands

Jika masih bermasalah:

```bash
# Cek semua sources
php artisan tinker -c "\App\Models\NewsSource::all(['id', 'name', 'slug', 'is_active'])->toArray()"

# Cek gaming sources saja
php artisan tinker -c "\App\Models\NewsSource::gaming()->get(['name', 'slug', 'is_active'])->toArray()"

# Test scraping semua gaming sources
php artisan news:scrape --gaming --dry-run
```

## ✅ Auto Content Creator Test

Setelah OneEsport fix, test auto content creator:
```bash
php artisan news:auto-create-content --limit=10
```

OneEsport akan otomatis termasuk dalam list sources dengan trust score 8.
