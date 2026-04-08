<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'AlertBot API',
        'version' => app()->version(),
        'status' => 'running',
        'timestamp' => now(),
        'endpoints' => [
            'health' => '/api/v1/health',
            'ping' => '/api/v1/ping',
            'debug' => '/api/v1/debug',
            'docs' => 'API documentation available at /api/v1/health'
        ]
    ]);
});
