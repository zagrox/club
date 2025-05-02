<?php

namespace App\Providers;

use App\Services\ZibalPayment;
use App\Services\MockZibalPayment;
use Illuminate\Support\ServiceProvider;

class ZibalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge Zibal config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/zibal.php', 'zibal'
        );

        // Register Zibal service
        $this->app->singleton('zibal', function ($app) {
            // Check if we should use the mock implementation
            if (
                config('zibal.mock', false) || 
                env('ZIBAL_MOCK', false) || 
                $app->environment('testing') || 
                config('zibal.merchant') === 'zibal-test-merchant-id'
            ) {
                return new MockZibalPayment();
            }
            
            return new ZibalPayment();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/zibal.php' => config_path('zibal.php'),
        ], 'zibal-config');
    }
} 