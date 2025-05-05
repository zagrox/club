<?php

// This script directly checks the database_notifications table

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Directly query the database_notifications table
echo "Directly querying database_notifications table:\n";
$results = DB::select('SELECT * FROM database_notifications');

if (count($results) > 0) {
    echo "Found " . count($results) . " notifications in database_notifications table:\n";
    
    foreach ($results as $notification) {
        echo "-------------------------------------------\n";
        echo "ID: {$notification->id}\n";
        echo "Type: {$notification->type}\n";
        echo "Notifiable Type: {$notification->notifiable_type}\n";
        echo "Notifiable ID: {$notification->notifiable_id}\n";
        echo "Read at: " . ($notification->read_at ?? 'Not read yet') . "\n";
        echo "Created at: {$notification->created_at}\n";
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "No records found in database_notifications table.\n";
}

echo "\nTest completed.\n"; 