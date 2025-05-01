<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register a binding for the permission model to fix the "Target class [permission] does not exist" error
        $this->app->bind('permission', function($app) {
            return $app->make(Permission::class);
        });
        
        // Also register a binding for the role model
        $this->app->bind('role', function($app) {
            return $app->make(Role::class);
        });
        
        // Explicitly bind the PermissionMatrixController
        $this->app->bind('App\Http\Controllers\PermissionMatrixController', function ($app) {
            return new \App\Http\Controllers\PermissionMatrixController();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        // Register permission check method on the gate
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
