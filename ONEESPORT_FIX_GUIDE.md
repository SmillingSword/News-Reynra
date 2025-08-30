# 🔧 OneEsport Scraping Fix Guide

## 🚨 Masalah yang Ditemukan

Ketika menjalankan command:
```bash
php artisan news:scrape --source="OneEsport" --force --sync
```

Hasilnya: **"No news sources found for scraping"**

## 🔍 Root Cause Analysis

1. **Missing `is_active` field**: Seeder tidak mengset `is_active = true`
2. **Missing `slug` field**: Model menggunakan `HasSlug` trait tapi slug tidak ter-generate
3. **Search by slug**: Command mencari berdasarkan slug, bukan nama

## ✅ Solusi yang Sudah Diterapkan

### 1. Update GamingNewsSourceSeeder
```php
// Menambahkan field yang hilang
$sourceData['is_active'] = true;
$sourceData['next_scrape_at'] = now();
```

### 2. Buat Command untuk Update Slugs
```bash
php artisan news:update-slugs
```

## 🚀 Langkah-langkah Perbaikan di Server

Jalankan command berikut di server secara berurutan:

### Step 1: Update Seeder dan Generate Slugs
```bash
# 1. Seed ulang dengan data yang benar
php artisan db:seed --class=GamingNewsSourceSeeder

# 2. Update slugs yang hilang
php artisan news:update-slugs

# 3. Verifikasi data
php artisan tinker
```

### Step 2: Verifikasi di Tinker
```php
// Cek semua news sources
\App\Models\NewsSource::all(['id', 'name', 'slug', 'is_active'])->toArray();

// Cek OneEsport spesifik
\App\Models\NewsSource::where('name', 'OneEsport')->first();

// Cek dengan slug
\App\Models\NewsSource::where('slug', 'oneesport')->first();
```

### Step 3: Test Scraping
```bash
# Test dengan nama yang benar
php artisan news:scrape --source="oneesport" --force --sync

# Atau test semua gaming sources
php artisan news:scrape --gaming --force --sync
```

## 📋 Kemungkinan Nama Slug OneEsport

Berdasarkan `Str::slug('OneEsport')`, kemungkinan slug-nya adalah:
- `oneesport`
- `one-esport` (jika ada spasi)

## 🔍 Debug Commands

### Cek News Sources yang Tersedia
```bash
php artisan tinker
```
```php
// Lihat semua sources
\App\Models\NewsSource::pluck('name', 'slug');

// Lihat yang aktif
\App\Models\NewsSource::active()->pluck('name', 'slug');

// Lihat gaming sources
\App\Models\NewsSource::gaming()->pluck('name', 'slug');
```

### Test Scraping dengan ID
```bash
# Jika tahu ID-nya (misal ID = 7)
php artisan news:scrape --source=7 --force --sync
```

## ✅ Expected Result Setelah Fix

```bash
[reynrast@anoa News-Reynra]$ php artisan news:scrape --source="oneesport" --force --sync
🚀 Starting news scraping process...
Found 1 news source(s) to scrape:
  • OneEsport (https://www.oneesports.id)

📰 Starting scraping jobs...
🔄 Scraping OneEsport synchronously...

✅ Scraping process completed!
   Jobs dispatched: 1
   Jobs skipped: 0
```

## 🎯 Auto Content Creator Integration

Setelah OneEsport berfungsi, akan otomatis termasuk dalam:
```bash
php artisan news:auto-create-content --limit=10
```

OneEsport akan menjadi salah satu source dengan trust_score tinggi (8/10).

## 📝 Notes

- OneEsport memiliki RSS feed: `https://www.oneesports.id/feed/`
- Trust score: 8 (High quality)
- Scraping frequency: 45 menit
- Max articles per scrape: 15
- Fokus pada: esports, mobile games, tournaments

## 🔄 Maintenance Commands

```bash
# Update semua slugs (force)
php artisan news:update-slugs --force

# Cek status semua sources
php artisan news:scrape --gaming --dry-run

# Test scraping semua sources
php artisan news:scrape --gaming --force --sync
