<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for alerts
        Gate::define('alerts.view', function (User $user) {
            $hasPermission = $user->hasPermission('alerts.view');
            \Log::info('Gate alerts.view check', ['user_id' => $user->id, 'has_permission' => $hasPermission]);
            return $hasPermission;
        });

        Gate::define('alerts.create', function (User $user) {
            $hasPermission = $user->hasPermission('alerts.create');
            \Log::info('Gate alerts.create check', ['user_id' => $user->id, 'has_permission' => $hasPermission]);
            return $hasPermission;
        });

        Gate::define('alerts.edit', function (User $user) {
            $hasPermission = $user->hasPermission('alerts.edit');
            \Log::info('Gate alerts.edit check', ['user_id' => $user->id, 'has_permission' => $hasPermission]);
            return $hasPermission;
        });

        Gate::define('alerts.delete', function (User $user) {
            $hasPermission = $user->hasPermission('alerts.delete');
            \Log::info('Gate alerts.delete check', ['user_id' => $user->id, 'has_permission' => $hasPermission]);
            return $hasPermission;
        });

        Gate::define('alerts.send', function (User $user) {
            $hasPermission = $user->hasPermission('alerts.send');
            \Log::info('Gate alerts.send check', ['user_id' => $user->id, 'has_permission' => $hasPermission]);
            return $hasPermission;
        });
    }
}