# News Scraper Bot - Gaming Indonesia

## Overview

Bot scraping berita gaming Indonesia yang dapat mengambil konten dari situs-situs berita gaming ternama, kemudian menulis ulang konten tersebut menggunakan AI untuk menghasilkan artikel original yang SEO-friendly.

## Features

### 🚀 Core Features
- **Web Scraping**: Menggunakan Guzzle HTTP client untuk scraping konten
- **DOM Parsing**: Symfony DomCrawler untuk parsing HTML
- **AI Content Rewriting**: OpenAI GPT untuk menulis ulang konten
- **Fallback Rewriting**: Sistem fallback tanpa AI jika OpenAI tidak tersedia
- **Queue Processing**: Background processing menggunakan Laravel Queue
- **Error Handling**: Comprehensive error handling dengan retry logic
- **Gaming Focus**: Filter khusus untuk konten gaming Indonesia

### 📰 Supported News Sources
- **Dunia Games** (duniagames.co.id) - Trust Score: 9/10
- **Esports.id** (esports.id) - Trust Score: 8/10
- **GGWP.id** (ggwp.id) - Trust Score: 7/10
- **MPL Indonesia** (mpl.id) - Trust Score: 9/10
- **GameBrott** (gamebrott.com) - Trust Score: 7/10
- **Jalantikus Gaming** (jalantikus.com/gaming) - Trust Score: 6/10

### 🎮 Gaming Categories
- Mobile Games (Mobile Legends, PUBG Mobile, Free Fire)
- PC Games (Valorant, Dota 2, CS:GO)
- Console Games
- Esports & Tournaments
- Game Reviews
- Gaming News
- Tips & Tricks
- Gaming Industry
- Game Streaming

## Installation & Setup

### 1. Dependencies
```bash
composer require guzzlehttp/guzzle symfony/dom-crawler openai-php/client
```

### 2. Database Migration
```bash
php artisan migrate
```

### 3. Seed Gaming News Sources
```bash
php artisan db:seed --class=GamingNewsSourceSeeder
```

### 4. Environment Configuration
Add to your `.env` file:
```env
# OpenAI Configuration (Optional)
OPENAI_API_KEY=your_openai_api_key_here

# Queue Configuration
QUEUE_CONNECTION=database
```

## Usage

### Command Line Interface

#### Basic Scraping
```bash
# Scrape all gaming news sources
php artisan news:scrape --gaming

# Dry run (preview what will be scraped)
php artisan news:scrape --dry-run --gaming

# Force scrape (ignore scheduling)
php artisan news:scrape --force --gaming

# Synchronous scraping (immediate execution)
php artisan news:scrape --sync --gaming

# Scrape specific source
php artisan news:scrape --source=dunia-games
```

#### Advanced Options
```bash
# Scrape specific source by ID
php artisan news:scrape --source=1

# Gaming sources only
php artisan news:scrape --gaming

# Force scrape even if not scheduled
php artisan news:scrape --force

# Run synchronously for immediate results
php artisan news:scrape --sync
```

### Admin Interface

Access the admin interface at: `/admin/news-scraper`

#### Available Routes:
- `GET /admin/news-scraper` - Dashboard
- `GET /admin/news-scraper/stats` - Statistics API
- `POST /admin/news-scraper/scrape-all` - Scrape all sources
- `GET /admin/news-scraper/sources/{id}` - Source details
- `POST /admin/news-scraper/sources/{id}/scrape` - Scrape specific source
- `PUT /admin/news-scraper/sources/{id}/config` - Update source config
- `POST /admin/news-scraper/sources/{id}/test` - Test scraping config

### Queue Processing

Start the queue worker to process scraping jobs:
```bash
php artisan queue:work
```

For production, use Supervisor or similar process manager.

## Configuration

### News Source Configuration

Each news source has configurable scraping parameters:

```php
'scraping_config' => [
    'selectors' => [
        'article_links' => '.post-item a, .article-item a',
        'title' => 'h1.entry-title, h1.post-title',
        'content' => '.entry-content, .post-content',
        'excerpt' => '.excerpt, .post-excerpt',
        'image' => '.featured-image img, .post-thumbnail img',
        'date' => '.post-date, .entry-date, time',
        'author' => '.author-name, .post-author',
    ],
    'filters' => [
        'min_content_length' => 300,
        'exclude_patterns' => ['iklan', 'advertisement', 'sponsored'],
        'required_keywords' => ['game', 'gaming', 'esport', 'mobile legends'],
    ],
    'limits' => [
        'max_articles_per_scrape' => 15,
        'request_delay' => 2, // seconds between requests
    ],
]
```

### Content Filters

```php
'content_filters' => [
    'min_word_count' => 100,
    'exclude_categories' => ['advertisement', 'sponsored'],
],
'keyword_filters' => [
    'include' => ['mobile legends', 'pubg mobile', 'free fire', 'valorant'],
    'exclude' => ['judi', 'betting', 'casino'],
]
```

## AI Content Rewriting

### OpenAI Integration

The bot uses OpenAI GPT-3.5-turbo for content rewriting with Indonesian gaming focus:

```php
// Example prompt structure
"Tulis ulang artikel berita gaming berikut dalam bahasa Indonesia yang menarik dan SEO-friendly:

JUDUL ASLI: {$title}
KATEGORI: {$category}

KONTEN ASLI:
{$content}

INSTRUKSI:
1. Tulis ulang dengan gaya yang fresh dan menarik untuk pembaca gaming Indonesia
2. Pertahankan semua fakta dan informasi penting
3. Gunakan bahasa yang mudah dipahami tapi tetap profesional
4. Tambahkan konteks gaming Indonesia jika relevan
5. Buat struktur yang jelas dengan paragraf yang mudah dibaca
6. Optimasi untuk SEO dengan keyword yang natural"
```

### Fallback Rewriting

If OpenAI is not available, the system uses basic text transformation:
- Synonym replacement for gaming terms
- Sentence restructuring
- Indonesian gaming terminology

## Database Schema

### News Sources Table
```sql
- id, name, slug, url, description, logo
- trust_score (1-10), type, is_active
- scraping_config (JSON)
- rss_url, scraping_frequency
- last_scraped_at, next_scrape_at
- auto_scraping_enabled
- content_filters, keyword_filters (JSON)
- total_scraped, successful_scrapes, failed_scrapes
- is_gaming_source, gaming_categories (JSON)
```

### Scraping Jobs Table
```sql
- id, news_source_id, status, type
- started_at, completed_at, duration_seconds
- articles_found, articles_processed, articles_created
- articles_updated, articles_skipped
- error_message, error_details (JSON)
- retry_count, next_retry_at
- scraping_metadata, performance_metrics (JSON)
```

## Monitoring & Logging

### Job Status Tracking
- **Pending**: Job queued but not started
- **Running**: Currently processing
- **Completed**: Successfully finished
- **Failed**: Error occurred (with retry logic)

### Statistics Available
- Total sources and active sources
- Jobs today (total, completed, failed)
- Articles created today
- Average processing duration
- Success rate percentage
- Source-specific performance metrics

### Error Handling
- Automatic retry with exponential backoff
- Maximum 3 retry attempts per job
- Detailed error logging with stack traces
- Failed job notifications
- Source auto-disable after repeated failures

## Performance Optimization

### Rate Limiting
- Configurable delay between requests (default: 2 seconds)
- Respectful scraping practices
- User-Agent rotation
- Request timeout handling

### Content Processing
- Duplicate detection by title and slug
- Content length validation
- Keyword filtering for relevance
- SEO optimization

### Queue Management
- High priority queue for trusted sources
- Background processing for scalability
- Job tagging for monitoring
- Timeout and memory management

## Security Considerations

### Ethical Scraping
- Respects robots.txt (when applicable)
- Rate limiting to avoid server overload
- User-Agent identification
- Content attribution in metadata

### Content Validation
- Malicious content filtering
- HTML sanitization
- Input validation
- XSS prevention

## Troubleshooting

### Common Issues

1. **OpenAI API Errors**
   ```bash
   # Check API key configuration
   php artisan tinker
   >>> config('services.openai.api_key')
   ```

2. **Queue Not Processing**
   ```bash
   # Start queue worker
   php artisan queue:work
   
   # Check failed jobs
   php artisan queue:failed
   ```

3. **Scraping Failures**
   ```bash
   # Test specific source
   php artisan news:scrape --source=dunia-games --sync
   
   # Check logs
   tail -f storage/logs/laravel.log
   ```

4. **Memory Issues**
   ```bash
   # Increase memory limit
   php -d memory_limit=512M artisan news:scrape
   ```

### Debug Mode
```bash
# Enable debug logging
php artisan news:scrape --gaming --sync -vvv
```

## Scheduled Automation

Add to your crontab for automated scraping:

```bash
# Every 30 minutes for high-priority sources
*/30 * * * * cd /path/to/project && php artisan news:scrape --gaming

# Daily cleanup of old jobs
0 2 * * * cd /path/to/project && php artisan queue:prune-failed --hours=168
```

## API Integration

### REST API Endpoints

```php
// Get scraping statistics
GET /admin/news-scraper/stats

// Trigger manual scraping
POST /admin/news-scraper/sources/{id}/scrape

// Get job details
GET /admin/news-scraper/jobs/{id}

// Retry failed job
POST /admin/news-scraper/jobs/{id}/retry
```

## Contributing

### Adding New News Sources

1. Add source to `GamingNewsSourceSeeder`
2. Configure selectors and filters
3. Test scraping configuration
4. Update documentation

### Extending Content Rewriting

1. Modify `ContentRewriterService`
2. Add new gaming keywords
3. Implement custom rewriting logic
4. Test with sample content

## License & Legal

- Respects website terms of service
- Content is rewritten for originality
- Attribution maintained in metadata
- Fair use compliance
- No commercial redistribution of original content

---

**Created by**: BlackBox AI Assistant  
**Version**: 1.0  
**Last Updated**: January 2025
