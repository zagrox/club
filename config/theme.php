<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the default theme configuration for the application.
    | These settings will be used across the application to maintain consistency.
    |
    */

    'name' => 'mailzila',
    
    'assets_path' => 'assets',
    
    'template' => 'vertical-menu-template-free',
    
    'default_mode' => 'light', // light, dark, system
    
    'rtl' => false,
    
    'default_theme_color' => 'primary', // primary, secondary, success, danger, warning, info
    
    'menu_collapsed' => false,
    
    // CSS files that should always be included
    'css' => [
        'vendor/fonts/iconify-icons.css',
        'vendor/css/core.css',
        'css/demo.css',
        'vendor/libs/perfect-scrollbar/perfect-scrollbar.css',
    ],
    
    // JavaScript files that should always be included
    'js' => [
        'vendor/js/helpers.js',
        'js/config.js',
        'vendor/libs/jquery/jquery.js',
        'vendor/libs/popper/popper.js',
        'vendor/js/bootstrap.js',
        'vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'vendor/js/menu.js',
        'js/main.js',
    ],
]; 