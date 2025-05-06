<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Language;
use Illuminate\Support\Facades\Schema;

class LanguageServiceProvider extends ServiceProvider
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
        // Only run if the languages table exists and we're not in the console
        if (Schema::hasTable('languages') && !$this->app->runningInConsole()) {
            // Load language config from the database
            Language::updateLocalizationConfig();
        }
    }
} 