#!/usr/bin/env bash
set -e

echo "Running deployment script..."

# Ensure we are in the right directory
cd /var/www/html

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set, generating one..."
    export APP_KEY=$(php artisan key:generate --show)
fi

echo "APP_KEY is set to: ${APP_KEY:0:10}..."

# Install composer dependencies
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions for storage and cache (just in case)
chown -R www-data:www-data storage bootstrap/cache

# Clear and cache configuration
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test database connection before running migrations
echo "Testing database connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connection successful'; } catch(Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); exit(1); }"

# Run migrations
echo "Running migrations..."
if php artisan migrate --force; then
    echo "Migrations completed successfully"
else
    echo "Migrations failed, but continuing deployment..."
fi

# Create storage link if it doesn't exist
php artisan storage:link || true

echo "Deployment script finished successfully."
# Start Apache in the foreground
exec apache2-foreground
