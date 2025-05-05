<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zibal Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the Zibal payment gateway.
    |
    */

    // API Key from Zibal dashboard
    // Using 'zibal-test-merchant-id' will automatically use the mock implementation
    'merchant' => env('ZIBAL_MERCHANT', 'zibal-test-merchant-id'),

    // API URLs
    'api_url' => [
        'request' => 'https://gateway.zibal.ir/v1/request',
        'verify' => 'https://gateway.zibal.ir/v1/verify',
        'start_pay' => 'https://gateway.zibal.ir/start/',
    ],

    // Currency code (IRR for Iranian Rial)
    'currency' => 'IRR',

    // Default callback URL
    'callback_url' => env('ZIBAL_CALLBACK_URL', '/payments/callback'),

    // Payment description prefix
    'description_prefix' => env('ZIBAL_DESCRIPTION_PREFIX', 'Payment for order: '),

    // Enable sandbox mode (for testing)
    'sandbox' => env('ZIBAL_SANDBOX', true),
    
    // Force mock mode (even if you provide a real merchant ID)
    'mock' => env('ZIBAL_MOCK', false),

    // Logging options
    'log_enabled' => env('ZIBAL_LOG_ENABLED', true),
    'log_channel' => env('ZIBAL_LOG_CHANNEL', 'daily'),
]; 