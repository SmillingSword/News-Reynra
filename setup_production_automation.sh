#!/bin/bash

echo "🚀 Setting up News Reynra Production Automation..."

# 1. Setup Queue Worker Service
echo "📋 Creating queue worker service..."
sudo tee /etc/systemd/system/news-reynra-worker.service > /dev/null <<EOF
[Unit]
Description=News Reynra Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/news-reynra
ExecStart=/usr/bin/php /var/www/news-reynra/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# 2. Enable and start the service
sudo systemctl daemon-reload
sudo systemctl enable news-reynra-worker
sudo systemctl start news-reynra-worker

# 3. Setup Cron Jobs
echo "⏰ Setting up cron jobs..."
(crontab -l 2>/dev/null; echo "# News Reynra Gaming Bot Automation") | crontab -
(crontab -l 2>/dev/null; echo "# Scrape gaming news every hour") | crontab -
(crontab -l 2>/dev/null; echo "0 * * * * cd /var/www/news-reynra && php artisan news:scrape --all >> /var/log/news-reynra-scraper.log 2>&1") | crontab -
(crontab -l 2>/dev/null; echo "# Auto-publish drafts every 30 minutes") | crontab -
(crontab -l 2>/dev/null; echo "*/30 * * * * cd /var/www/news-reynra && php artisan articles:publish-drafts --confirm >> /var/log/news-reynra-publisher.log 2>&1") | crontab -
(crontab -l 2>/dev/null; echo "# Clean up old scraping jobs daily") | crontab -
(crontab -l 2>/dev/null; echo "0 2 * * * cd /var/www/news-reynra && php artisan queue:prune-batches --hours=48 >> /var/log/news-reynra-cleanup.log 2>&1") | crontab -

# 4. Create log directories
sudo mkdir -p /var/log/news-reynra
sudo chown www-data:www-data /var/log/news-reynra

echo "✅ Production automation setup complete!"
echo ""
echo "🎯 AUTOMATION SCHEDULE:"
echo "- Gaming news scraping: Every hour"
echo "- Auto-publish articles: Every 30 minutes"
echo "- Cleanup old jobs: Daily at 2 AM"
echo ""
echo "📊 MONITORING COMMANDS:"
echo "- Check queue worker: sudo systemctl status news-reynra-worker"
echo "- View scraper logs: tail -f /var/log/news-reynra-scraper.log"
echo "- View publisher logs: tail -f /var/log/news-reynra-publisher.log"
echo "- Check cron jobs: crontab -l"
echo ""
echo "🔧 MANUAL COMMANDS:"
echo "- Test scraping: php artisan news:scrape --source=gamebrott"
echo "- Force publish: php artisan articles:publish-drafts --confirm"
echo "- Check articles: php artisan articles:show-latest"
