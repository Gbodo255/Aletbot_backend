#!/usr/bin/env bash
echo "Running deployment script..."

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

echo "Deployment script finished."
# Start Apache in the foreground
apache2-foreground
