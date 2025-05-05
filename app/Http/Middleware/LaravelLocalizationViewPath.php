<?php

namespace App\Http\Middleware;

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath as BaseLaravelLocalizationViewPath;

class LaravelLocalizationViewPath extends BaseLaravelLocalizationViewPath
{
    // This class simply extends the package's middleware
    // It's a wrapper to ensure Laravel can find the class by its alias
} 