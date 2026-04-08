#!/usr/bin/env bash
set -e

echo "Running deployment script..."

# Ensure we are in the right directory
cd /var/www/html

# Install composer dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions for storage and cache (just in case)
chown -R www-data:www-data storage bootstrap/cache

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage link if it doesn't exist
php artisan storage:link || true

echo "Deployment script finished."
# Start Apache in the foreground
exec apache2-foreground
