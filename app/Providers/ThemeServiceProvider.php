<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Blade;

class ThemeServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(resource_path('views/themes/mailzila'), 'theme');
        
        // Register theme helper directives
        $this->registerBladeDirectives();
        
        View::composer('*', function ($view) {
            // Get default theme configuration
            $themeConfig = config('theme') ?? [];
            
            // Ensure defaults are set
            $themeConfig['mode'] = $themeConfig['mode'] ?? $themeConfig['default_mode'] ?? 'light';
            $themeConfig['is_rtl'] = $themeConfig['is_rtl'] ?? $themeConfig['rtl'] ?? false;
            $themeConfig['menu_collapsed'] = $themeConfig['menu_collapsed'] ?? false;
            $themeConfig['assets_path'] = $themeConfig['assets_path'] ?? 'assets';
            $themeConfig['template'] = $themeConfig['template'] ?? 'vertical-menu-template-free';
            
            // Override with user preferences from cookies if available
            $cookieMode = Cookie::get('theme-mode');
            if ($cookieMode) {
                $themeConfig['mode'] = $cookieMode;
            }
            
            $cookieRtl = Cookie::get('theme-rtl');
            if ($cookieRtl !== null) {
                $themeConfig['is_rtl'] = $cookieRtl === 'true';
            }
            
            $cookieCollapsed = Cookie::get('theme-menu-collapsed');
            if ($cookieCollapsed !== null) {
                $themeConfig['menu_collapsed'] = $cookieCollapsed === 'true';
            }
            
            $view->with('themeConfig', $themeConfig);
        });
    }
    
    /**
     * Register custom Blade directives for theme functionality
     */
    protected function registerBladeDirectives()
    {
        // Directive for theme assets
        Blade::directive('themeAsset', function ($expression) {
            return "<?php echo asset('assets/' . {$expression}); ?>";
        });
        
        // Directive for theme-specific classes
        Blade::directive('themeClass', function ($expression) {
            return "<?php echo \$themeConfig['mode'] === 'dark' ? {$expression} : ''; ?>";
        });
        
        // Directive for conditional theme mode rendering
        Blade::directive('ifThemeMode', function ($expression) {
            return "<?php if(\$themeConfig['mode'] === {$expression}): ?>";
        });
        
        Blade::directive('endifThemeMode', function () {
            return "<?php endif; ?>";
        });
    }
} 