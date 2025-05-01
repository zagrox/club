<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
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
        
        // Register Sanctum token abilities
        Sanctum::tokensCan([
            'read:users' => 'Read users information',
            'write:users' => 'Create or update users',
            'delete:users' => 'Delete users',
            'read:roles' => 'Read roles information',
            'write:roles' => 'Create or update roles',
            'delete:roles' => 'Delete roles',
            'read:orders' => 'Read orders information',
            'write:orders' => 'Create or update orders',
            'delete:orders' => 'Delete orders',
            'read:notifications' => 'Read notifications',
            'write:notifications' => 'Create or update notifications',
        ]);
    }
} 