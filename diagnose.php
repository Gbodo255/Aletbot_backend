<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Laravel Deployment Diagnostic ===\n\n";

echo "1. Environment:\n";
echo "   APP_ENV: " . config('app.env', 'not set') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "   APP_KEY: " . (config('app.key') ? 'set (' . substr(config('app.key'), 0, 10) . '...)' : 'NOT SET') . "\n\n";

echo "2. Database Configuration:\n";
echo "   DB_CONNECTION: " . config('database.default', 'not set') . "\n";
echo "   DB_HOST: " . config('database.connections.pgsql.host', 'not set') . "\n";
echo "   DB_PORT: " . config('database.connections.pgsql.port', 'not set') . "\n";
echo "   DB_DATABASE: " . config('database.connections.pgsql.database', 'not set') . "\n";
echo "   DB_USERNAME: " . config('database.connections.pgsql.username', 'not set') . "\n\n";

echo "3. Testing Database Connection:\n";
try {
    \DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n";
} catch (\Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n4. Cache Status:\n";
echo "   Config cached: " . (file_exists(storage_path('framework/cache/config.php')) ? 'yes' : 'no') . "\n";
echo "   Routes cached: " . (file_exists(storage_path('framework/cache/routes.php')) ? 'yes' : 'no') . "\n";
echo "   Views cached: " . (file_exists(storage_path('framework/cache/views.php')) ? 'yes' : 'no') . "\n\n";

echo "5. Migrations Status:\n";
try {
    $migrations = \DB::table('migrations')->count();
    echo "   ✅ Migrations table exists with {$migrations} records\n";
} catch (\Exception $e) {
    echo "   ❌ Migrations table issue: " . $e->getMessage() . "\n";
}

echo "\n=== Diagnostic Complete ===\n";