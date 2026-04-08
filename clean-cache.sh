#!/usr/bin/env bash
set -e

cd /var/www/html

echo "Clearing Laravel caches..."

# Clear all caches
php artisan cache:clear --quiet
php artisan config:clear --quiet
php artisan route:clear --quiet
php artisan view:clear --quiet

# Clear file-based caches
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*

echo "Caches cleared successfully"
