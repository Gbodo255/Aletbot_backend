#!/usr/bin/env php
<?php
/**
 * Setup script to initialize the database without external dependencies
 */

$basePath = __DIR__;

// Load the framework
require $basePath . '/vendor/autoload.php';

try {
    // Create Laravel app
    $app = require_once $basePath . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

    // Run migrations
    echo "Running migrations...\n";
    $kernel->call('migrate:fresh', ['--seed' => true]);
    
    echo "\n✓ Database setup completed successfully!\n";
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
