<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ForceLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the current locale from the URL segment
        $locale = $request->segment(1);
        
        // If the locale is valid and supported
        if ($locale && in_array($locale, array_keys(LaravelLocalization::getSupportedLocales()))) {
            // Set the application locale
            app()->setLocale($locale);
            
            // Log for debugging
            \Log::info('Force locale middleware - Set locale: ' . $locale);
        } else {
            // Log for debugging
            \Log::info('Force locale middleware - Invalid locale or no locale in URL: ' . $locale);
        }
        
        return $next($request);
    }
} 