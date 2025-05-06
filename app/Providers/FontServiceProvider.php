<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FontServiceProvider extends ServiceProvider
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
        try {
            // Skip in console
            if ($this->app->runningInConsole()) {
                return;
            }
            
            // Check if the font file exists in public directory
            $publicFontPath = public_path('fonts/zagrox.ttf');
            
            // If the font file doesn't exist in public, try to copy it
            if (!File::exists($publicFontPath)) {
                $sourceFontPath = resource_path('fonts/zagrox.ttf');
                
                // Check if source font exists
                if (File::exists($sourceFontPath)) {
                    // Create fonts directory if it doesn't exist
                    $fontsDir = public_path('fonts');
                    if (!File::isDirectory($fontsDir)) {
                        File::makeDirectory($fontsDir, 0755, true);
                    }
                    
                    // Copy the font file
                    File::copy($sourceFontPath, $publicFontPath);
                    Log::info('Font file published during application boot', ['path' => $publicFontPath]);
                } else {
                    Log::warning('Source font file not found during automatic publishing', ['path' => $sourceFontPath]);
                }
            }
        } catch (\Exception $e) {
            // Don't let font publishing error break the application
            Log::error('Error publishing font file', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 