<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AlertController;

// Public routes
Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', function () {
        try {
            // Test database connection
            \DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }

        // Get migrations count
        try {
            $migrationsCount = \DB::table('migrations')->count();
            $migrationsStatus = "{$migrationsCount} migrations";
        } catch (\Exception $e) {
            $migrationsStatus = 'error: ' . $e->getMessage();
        }

        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'database' => $dbStatus,
            'migrations' => $migrationsStatus,
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'version' => app()->version(),
            'php_version' => PHP_VERSION,
            'server' => [
                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'unknown',
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]
        ]);
    });

    // Debug endpoint (remove in production)
    Route::get('/debug', function () {
        return response()->json([
            'env' => [
                'APP_ENV' => env('APP_ENV'),
                'APP_DEBUG' => env('APP_DEBUG'),
                'APP_KEY' => env('APP_KEY') ? 'set' : 'not set',
                'DB_CONNECTION' => env('DB_CONNECTION'),
                'DB_HOST' => env('DB_HOST'),
                'DB_DATABASE' => env('DB_DATABASE'),
            ],
            'config' => [
                'app.env' => config('app.env'),
                'app.debug' => config('app.debug'),
                'database.default' => config('database.default'),
            ],
            'timestamp' => now(),
        ]);
    });

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);
        Route::put('/profile/notification-preferences', [ProfileController::class, 'updateNotificationPreferences']);
        Route::put('/profile/change-password', [ProfileController::class, 'changePassword']);

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/activity-logs/user/{userId}', [ActivityLogController::class, 'userActivity']);
        Route::get('/activity-logs/{log}', [ActivityLogController::class, 'show']);

        // Users (Admin only)
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::post('/{user}/assign-role', [UserController::class, 'assignRole']);
            Route::post('/{user}/remove-role', [UserController::class, 'removeRole']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });

        // Roles (Admin only)
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::post('/{role}/assign-permission', [RoleController::class, 'assignPermission']);
            Route::post('/{role}/remove-permission', [RoleController::class, 'removePermission']);
            Route::delete('/{role}', [RoleController::class, 'destroy']);
        });

        // Alerts
        Route::prefix('alerts')->group(function () {
            Route::get('/history', [AlertController::class, 'history']);
            Route::get('/', [AlertController::class, 'index']);
            Route::post('/', [AlertController::class, 'store']);
            Route::get('/{alert}', [AlertController::class, 'show']);
            Route::put('/{alert}', [AlertController::class, 'update']);
            Route::post('/{alert}/send', [AlertController::class, 'send']);
            Route::delete('/{alert}', [AlertController::class, 'destroy']);
        });
    });
});
