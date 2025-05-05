<?php

namespace App\Http\Middleware;

use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect as BaseLocaleCookieRedirect;

class LocaleCookieRedirect extends BaseLocaleCookieRedirect
{
    // This class simply extends the package's middleware
    // It's a wrapper to ensure Laravel can find the class by its alias
} 