<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gates for roles
        Gate::define('role', function ($user, $role) {
            return $user->hasRole($role);
        });

        // Define gates for permissions
        Gate::define('permission', function ($user, $permission) {
            return $user->hasPermissionTo($permission);
        });
        
        // Token abilities are defined in config/sanctum.php
    }
} 