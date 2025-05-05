<?php

namespace App\Http\Middleware;

use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect as BaseLocaleSessionRedirect;

class LocaleSessionRedirect extends BaseLocaleSessionRedirect
{
    // This class simply extends the package's middleware
    // It's a wrapper to ensure Laravel can find the class by its alias
} 