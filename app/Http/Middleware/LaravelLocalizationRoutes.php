<?php

namespace App\Http\Middleware;

use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes as BaseLaravelLocalizationRoutes;

class LaravelLocalizationRoutes extends BaseLaravelLocalizationRoutes
{
    // This class simply extends the package's middleware
    // It's a wrapper to ensure Laravel can find the class by its alias
} 