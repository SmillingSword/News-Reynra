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
# News Reynra Automation System
# Auto-create content with SEO & hashtags - every 2 hours
0 */2 * * * cd $CURRENT_DIR && php artisan news:auto-create-content --limit=3 >> /var/log/news-reynra-auto-creator.log 2>&1

# Clean up old scraping jobs daily at 2 AM
0 2 * * * cd $CURRENT_DIR && php artisan queue:prune-batches --hours=48 >> /var/log/news-reynra-cleanup.log 2>&1

# Check system status daily at 6 AM
0 6 * * * cd $CURRENT_DIR && php artisan production:status >> /var/log/news-reynra-status.log 2>&1
EOF

# Install new crontab
echo "📥 Installing new crontab..."
crontab /tmp/new_crontab

# Create log directory if not exists
echo "📁 Creating log directory..."
sudo mkdir -p /var/log
sudo touch /var/log/news-reynra-auto-creator.log
sudo touch /var/log/news-reynra-cleanup.log  
sudo touch /var/log/news-reynra-status.log
sudo chown $USER:$USER /var/log/news-reynra-*.log

echo "✅ Cronjob automation setup completed!"
echo ""
echo "📋 Current crontab:"
crontab -l
echo ""
echo "📊 Automation Schedule:"
echo "  🔄 Auto-create content: Every 2 hours"
echo "  🧹 Cleanup old jobs: Daily at 2 AM"
echo "  📈 Status check: Daily at 6 AM"
echo ""
echo "📝 Log files:"
echo "  📄 Auto-creator: /var/log/news-reynra-auto-creator.log"
echo "  🧹 Cleanup: /var/log/news-reynra-cleanup.log"
echo "  📊 Status: /var/log/news-reynra-status.log"
echo ""
echo "🎉 News Reynra is now fully automated!"
