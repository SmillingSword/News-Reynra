# 🚀 News Reynra Production Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. Server Requirements
- ✅ PHP 8.1+ with extensions: curl, gd, mbstring, xml, zip
- ✅ MySQL 8.0+ or MariaDB 10.3+
- ✅ Redis for queue management
- ✅ Nginx or Apache web server
- ✅ Supervisor for queue workers
- ✅ Cron for scheduled tasks

### 2. Environment Setup
```bash
# Clone repository
git clone https://github.com/your-repo/news-reynra.git
cd news-reynra

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
```bash
# Run migrations
php artisan migrate --force

# Seed gaming news sources
php artisan db:seed --class=GamingNewsSourceSeeder

# Seed categories and tags
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=TagSeeder
```

## 🔧 Production Automation Setup

### 1. Run Automation Script
```bash
chmod +x setup_production_automation.sh
sudo ./setup_production_automation.sh
```

### 2. Manual Setup (Alternative)

#### Queue Worker Service
```bash
# Create systemd service
sudo nano /etc/systemd/system/news-reynra-worker.service

# Add service configuration (see setup_production_automation.sh)

# Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable news-reynra-worker
sudo systemctl start news-reynra-worker
```

#### Cron Jobs
```bash
# Edit crontab
crontab -e

# Add these lines:
0 * * * * cd /var/www/news-reynra && php artisan news:scrape --all >> /var/log/news-reynra-scraper.log 2>&1
*/30 * * * * cd /var/www/news-reynra && php artisan articles:publish-drafts --confirm >> /var/log/news-reynra-publisher.log 2>&1
0 2 * * * cd /var/www/news-reynra && php artisan queue:prune-batches --hours=48 >> /var/log/news-reynra-cleanup.log 2>&1
```

## 📊 Production Commands

### Daily Operations
```bash
# Check production status
php artisan production:status

# Manual scraping (if needed)
php artisan news:scrape --source=gamebrott
php artisan news:scrape --all

# Force publish drafts
php artisan articles:publish-drafts --confirm

# Check latest articles
php artisan articles:show-latest
php artisan articles:show-full

# Test SEO generation
php artisan seo:test --title="Your Test Title"
```

### Monitoring Commands
```bash
# Check queue worker status
sudo systemctl status news-reynra-worker

# View real-time logs
tail -f /var/log/news-reynra-scraper.log
tail -f /var/log/news-reynra-publisher.log
tail -f storage/logs/laravel.log

# Check cron jobs
crontab -l

# Monitor system resources
htop
df -h
```

### Troubleshooting Commands
```bash
# Restart queue worker
sudo systemctl restart news-reynra-worker

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check failed jobs
php artisan queue:failed
php artisan queue:retry all

# Database maintenance
php artisan queue:prune-batches --hours=48
```

## 🎯 Automation Schedule

| Task | Frequency | Command | Purpose |
|------|-----------|---------|---------|
| **News Scraping** | Every hour | `news:scrape --all` | Fetch latest gaming news |
| **Auto Publish** | Every 30 min | `articles:publish-drafts` | Publish scraped articles |
| **Cleanup** | Daily 2 AM | `queue:prune-batches` | Clean old queue jobs |

## 📈 Performance Optimization

### 1. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_articles_status_published ON articles(status, published_at);
CREATE INDEX idx_articles_created_at ON articles(created_at);
CREATE INDEX idx_scraping_jobs_status ON scraping_jobs(status, created_at);
```

### 2. Caching Configuration
```bash
# Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000

# Redis configuration for queues
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Web Server Configuration (Nginx)
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/news-reynra/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Cache static assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## 🔒 Security Considerations

### 1. Environment Variables
```bash
# Set secure environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong database credentials
DB_PASSWORD=your-secure-password

# Configure rate limiting
RATE_LIMIT_PER_MINUTE=60
```

### 2. File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/news-reynra
sudo chmod -R 755 /var/www/news-reynra
sudo chmod -R 775 /var/www/news-reynra/storage
sudo chmod -R 775 /var/www/news-reynra/bootstrap/cache
```

## 📱 SEO & Social Media Features

### Automatic SEO Generation
- ✅ **Meta titles** with viral prefixes
- ✅ **Meta descriptions** with hashtags
- ✅ **Gaming keywords** extraction
- ✅ **8 viral hashtags** per article
- ✅ **Open Graph tags** for Facebook
- ✅ **Twitter Cards** for Twitter sharing

### Hashtag Examples
```
#ResidentEvil #GamingIndonesia #EsportsID #ViralGaming
#TrendingNow #GamersIndonesia #BeritaGaming #HotNews
```

## 🚨 Monitoring & Alerts

### 1. Log Monitoring
```bash
# Setup log rotation
sudo nano /etc/logrotate.d/news-reynra

# Add configuration:
/var/log/news-reynra*.log {
    daily
    rotate 30
    compress
    delaycompress
    missingok
    notifempty
    create 644 www-data www-data
}
```

### 2. Health Checks
```bash
# Add to crontab for health monitoring
*/5 * * * * cd /var/www/news-reynra && php artisan production:status > /tmp/news-reynra-health.txt
```

## 🎮 Gaming Sources Configuration

### Active Sources
1. **GameBrott** (gamebrott.com) - ✅ Tested & Working
2. **Dunia Games** (duniagames.co.id) - 🔧 Configured
3. **Esports.id** (esports.id) - 🔧 Configured
4. **GGWP.id** (ggwp.id) - 🔧 Configured
5. **MPL Indonesia** (mpl.id) - 🔧 Configured

### Adding New Sources
```bash
# Add via admin panel or command
php artisan tinker
NewsSource::create([
    'name' => 'New Gaming Site',
    'url' => 'https://newgamingsite.com',
    'is_active' => true,
    'scraping_config' => [
        'article_selector' => '.article',
        'title_selector' => 'h1',
        'content_selector' => '.content'
    ]
]);
```

## 📞 Support & Maintenance

### Emergency Contacts
- **System Admin**: your-admin@domain.com
- **Developer**: your-dev@domain.com

### Backup Strategy
```bash
# Daily database backup
0 3 * * * mysqldump -u username -p password news_reynra > /backups/news_reynra_$(date +\%Y\%m\%d).sql

# Weekly file backup
0 4 * * 0 tar -czf /backups/news_reynra_files_$(date +\%Y\%m\%d).tar.gz /var/www/news-reynra
```

---

## 🎉 Ready for Production!

Your News Reynra gaming bot is now ready for production with:
- ✅ **Automated scraping** every hour
- ✅ **Auto-publishing** every 30 minutes  
- ✅ **Viral SEO hashtags** for maximum reach
- ✅ **Professional monitoring** and logging
- ✅ **Scalable architecture** for growth

**Happy Gaming News Automation!** 🎮🚀
