<?php

namespace App\Http\Middleware;

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter as BaseLaravelLocalizationRedirectFilter;

class LaravelLocalizationRedirectFilter extends BaseLaravelLocalizationRedirectFilter
{
    // This class simply extends the package's middleware
    // It's a wrapper to ensure Laravel can find the class by its alias
} 