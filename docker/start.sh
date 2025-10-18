#!/bin/bash

# Start Laravel application
cd /var/www/html

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Run Laravel optimizations
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run database migrations (skip if database not available)
php artisan migrate --force || echo "Database migration skipped - will retry on first request"

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
