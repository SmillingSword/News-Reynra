#!/bin/bash

echo "🚀 Setting up News Reynra Automation Cronjobs..."

# Get current directory
CURRENT_DIR="/home/reynrast/news.reynrastore.com/News-Reynra"

# Create backup of current crontab
echo "📋 Backing up current crontab..."
crontab -l > /tmp/crontab_backup_$(date +%Y%m%d_%H%M%S)

# Create new crontab with automation
echo "⚙️  Creating new crontab with automation..."
cat > /tmp/new_crontab << EOF
# News Reynra INTENSIVE Automation System
# AGGRESSIVE CONTENT CREATION: 10 articles every hour for maximum content generation
# This will create approximately 240 articles per day = 1,680 articles per week

# Main content creation - Every hour at minute 0 (10 articles)
0 * * * * cd $CURRENT_DIR && php artisan news:auto-create-content --limit=10 >> /var/log/news-reynra-hourly-main.log 2>&1

# Backup content creation - Every hour at minute 30 (5 more articles)
30 * * * * cd $CURRENT_DIR && php artisan news:auto-create-content --limit=5 >> /var/log/news-reynra-hourly-backup.log 2>&1

# Publish any stuck draft articles - Every 3 hours
0 */3 * * * cd $CURRENT_DIR && php artisan articles:publish-drafts >> /var/log/news-reynra-publish-drafts.log 2>&1

# Clean up old scraping jobs daily at 2 AM
0 2 * * * cd $CURRENT_DIR && php artisan queue:prune-batches --hours=48 >> /var/log/news-reynra-cleanup.log 2>&1

# Check system status daily at 6 AM
0 6 * * * cd $CURRENT_DIR && php artisan production:status >> /var/log/news-reynra-status.log 2>&1

# Weekly content report - Every Sunday at 3 AM
0 3 * * 0 cd $CURRENT_DIR && php artisan news:weekly-report >> /var/log/news-reynra-weekly-report.log 2>&1
EOF

# Install new crontab
echo "📥 Installing new crontab..."
crontab /tmp/new_crontab

# Create log directory if not exists
echo "📁 Creating log directory..."
sudo mkdir -p /var/log
sudo touch /var/log/news-reynra-hourly-main.log
sudo touch /var/log/news-reynra-hourly-backup.log
sudo touch /var/log/news-reynra-publish-drafts.log
sudo touch /var/log/news-reynra-cleanup.log  
sudo touch /var/log/news-reynra-status.log
sudo touch /var/log/news-reynra-weekly-report.log
sudo chown $USER:$USER /var/log/news-reynra-*.log

# Create monitoring script
cat > monitor_intensive_creation.sh << 'EOF'
#!/bin/bash
echo "📊 News Reynra - INTENSIVE Content Creation Monitor"
echo "=================================================="
echo ""
echo "📰 Content Statistics:"
php artisan tinker --execute="
\$today = \App\Models\Article::whereDate('created_at', today())->count();
echo 'Today: ' . \$today . ' articles';
echo PHP_EOL;
\$yesterday = \App\Models\Article::whereDate('created_at', today()->subDay())->count();
echo 'Yesterday: ' . \$yesterday . ' articles';
echo PHP_EOL;
\$thisWeek = \App\Models\Article::where('created_at', '>=', now()->subWeek())->count();
echo 'This week: ' . \$thisWeek . ' articles';
echo PHP_EOL;
\$lastHour = \App\Models\Article::where('created_at', '>=', now()->subHour())->count();
echo 'Last hour: ' . \$lastHour . ' articles';
"
echo ""
echo "📋 Active Cron Jobs:"
crontab -l | grep "news:auto-create-content"
echo ""
echo "📝 Recent Logs (Last 3 entries):"
echo "--- Main Hourly Creation ---"
tail -3 /var/log/news-reynra-hourly-main.log 2>/dev/null || echo "No main logs yet"
echo ""
echo "--- Backup Hourly Creation ---"
tail -3 /var/log/news-reynra-hourly-backup.log 2>/dev/null || echo "No backup logs yet"
EOF

chmod +x monitor_intensive_creation.sh

echo "✅ INTENSIVE Cronjob automation setup completed!"
echo ""
echo "📋 Current crontab:"
crontab -l
echo ""
echo "🚀 INTENSIVE Automation Schedule:"
echo "  ⚡ Main creation: Every hour at :00 → 10 articles"
echo "  🔄 Backup creation: Every hour at :30 → 5 articles"
echo "  📊 Total: 15 articles/hour = 360/day = 2,520/week"
echo "  📝 Publish drafts: Every 3 hours"
echo "  🧹 Cleanup: Daily at 2 AM"
echo "  📈 Status check: Daily at 6 AM"
echo "  📊 Weekly report: Sundays at 3 AM"
echo ""
echo "📝 Log files:"
echo "  ⚡ Main hourly: /var/log/news-reynra-hourly-main.log"
echo "  🔄 Backup hourly: /var/log/news-reynra-hourly-backup.log"
echo "  📝 Publish drafts: /var/log/news-reynra-publish-drafts.log"
echo "  🧹 Cleanup: /var/log/news-reynra-cleanup.log"
echo "  📊 Status: /var/log/news-reynra-status.log"
echo "  📊 Weekly report: /var/log/news-reynra-weekly-report.log"
echo ""
echo "🛠️  Management Commands:"
echo "  ./monitor_intensive_creation.sh - Monitor content creation stats"
echo "  tail -f /var/log/news-reynra-hourly-main.log - Watch main creation live"
echo ""
echo "⚠️  IMPORTANT: This creates 2,520+ articles per week!"
echo "🎉 News Reynra INTENSIVE automation is now active!"
