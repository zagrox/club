<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection
    |--------------------------------------------------------------------------
    |
    | This option defines which queue connection the notification jobs
    | will be dispatched to. The connection must be defined in the
    | queue configuration file config/queue.php.
    |
    */
    
    'queue' => [
        'connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'database'),
        'name' => env('NOTIFICATION_QUEUE_NAME', 'notifications'),
        'tries' => env('NOTIFICATION_QUEUE_TRIES', 3),
        'backoff' => env('NOTIFICATION_QUEUE_BACKOFF', 60),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Status Values
    |--------------------------------------------------------------------------
    |
    | This option defines the allowed status values for notifications.
    |
    */
    
    'statuses' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'sent' => 'Sent',
        'failed' => 'Failed',
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'archived' => 'Archived',
        'canceled' => 'Canceled',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Priority Levels
    |--------------------------------------------------------------------------
    |
    | This option defines the priority levels for notifications.
    |
    */
    
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | This option defines the categories for notifications.
    |
    */
    
    'categories' => [
        'system' => 'System',
        'update' => 'Update',
        'reminder' => 'Reminder',
        'custom' => 'Custom',
    ],
    
]; 