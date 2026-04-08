#!/usr/bin/env bash
set -e

echo "Running deployment script..."

# Log environment information
echo "=== Environment Information ==="
echo "APP_ENV: $APP_ENV"
echo "APP_DEBUG: $APP_DEBUG"
echo "APP_KEY: ${APP_KEY:0:20}..."
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"
echo "PORT: $PORT"
echo "================================"

# Ensure we are in the right directory
cd /var/www/html

# Check if APP_KEY is set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "APP_KEY is not properly set, generating a new one..."
    php artisan key:generate --force
    export APP_KEY=$(php artisan key:generate --show)
    echo "Generated APP_KEY: ${APP_KEY:0:20}..."
else
    echo "APP_KEY is already set: ${APP_KEY:0:20}..."
fi

# Install composer dependencies if missing
if [ ! -f vendor/autoload.php ]; then
    echo "Installing dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "Dependencies already installed, skipping composer install."
fi

# Set permissions for storage and cache (just in case)
chown -R www-data:www-data storage bootstrap/cache

# Clear all caches first
echo "Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Then rebuild caches
echo "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configure Apache to use Render's assigned port if necessary
if [ -n "$PORT" ] && [ "$PORT" != "80" ]; then
    echo "Configuring Apache to listen on port $PORT"
    sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/" /etc/apache2/sites-available/000-default.conf
fi

# Test database connection before running migrations
echo "Testing database connection..."
if php -r "
try {
    require 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    DB::connection()->getPdo();
    echo 'Database connection successful';
} catch(Exception \$e) {
    echo 'Database connection failed: ' . \$e->getMessage();
    exit(1);
}
"; then
    echo "Database connection test passed"
else
    echo "Database connection test failed, but continuing..."
fi

# Run migrations
echo "Running migrations..."
if php artisan migrate --force; then
    echo "Migrations completed successfully"
else
    echo "Migrations failed, but continuing deployment..."
fi

# Create storage link if it doesn't exist
php artisan storage:link || true

# Test if Laravel can boot properly
echo "Testing Laravel application boot..."
if php artisan --version; then
    echo "Laravel application booted successfully"
else
    echo "Laravel application failed to boot"
fi

# Clear any remaining caches and test a simple route
echo "Testing application with a simple artisan command..."
php artisan route:list --compact | head -5

echo "Deployment script finished successfully."
# Start Apache in the foreground
exec apache2-foreground
