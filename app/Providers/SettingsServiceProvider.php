<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run if the settings table exists
        if (Schema::hasTable('settings')) {
            // Load Zibal settings from the database
            $zibalSettings = Setting::getGroup('zibal');
            
            if (!empty($zibalSettings)) {
                // Set Zibal configuration values from settings
                config([
                    'zibal.merchant' => $zibalSettings['zibal_merchant'] ?? config('zibal.merchant'),
                    'zibal.sandbox' => filter_var($zibalSettings['zibal_sandbox'] ?? config('zibal.sandbox'), FILTER_VALIDATE_BOOLEAN),
                    'zibal.mock' => filter_var($zibalSettings['zibal_mock'] ?? config('zibal.mock'), FILTER_VALIDATE_BOOLEAN),
                    'zibal.callback_url' => $zibalSettings['zibal_callback_url'] ?? config('zibal.callback_url'),
                    'zibal.description_prefix' => $zibalSettings['zibal_description_prefix'] ?? config('zibal.description_prefix'),
                    'zibal.log_enabled' => filter_var($zibalSettings['zibal_log_enabled'] ?? config('zibal.log_enabled'), FILTER_VALIDATE_BOOLEAN),
                    'zibal.log_channel' => $zibalSettings['zibal_log_channel'] ?? config('zibal.log_channel'),
                ]);
            }
        }
    }
} 