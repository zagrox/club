<?php

// This script checks for database notifications

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

// Find an existing user
$user = User::first();

if (!$user) {
    echo "No user found. Please create a user first.\n";
    exit;
}

echo "Checking database notifications for user: {$user->name} ({$user->email})\n";

// List all tables in the database
echo "Database tables:\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values(get_object_vars($table))[0];
    echo "- {$tableName}\n";
}

// Check for database notifications in database_notifications
$notifications = DB::table('database_notifications')
    ->where('notifiable_type', User::class)
    ->where('notifiable_id', $user->id)
    ->get();

echo "\nNotifications from database_notifications table:\n";
if ($notifications->count() > 0) {
    echo "Found {$notifications->count()} notifications:\n";
    
    foreach ($notifications as $notification) {
        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
        echo "-------------------------------------------\n";
        echo "ID: {$notification->id}\n";
        echo "Type: {$notification->type}\n";
        echo "Read at: " . ($notification->read_at ? $notification->read_at : 'Not read yet') . "\n";
        echo "Created at: {$notification->created_at}\n";
        echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "No notifications found in database_notifications for this user.\n";
}

// Check for user's notifications through Laravel's notification relationship
echo "\nNotifications from User->notifications() relationship:\n";
$laravelNotifications = $user->notifications;
if ($laravelNotifications->count() > 0) {
    echo "Found {$laravelNotifications->count()} Laravel notifications:\n";
    
    foreach ($laravelNotifications as $notification) {
        echo "-------------------------------------------\n";
        echo "ID: {$notification->id}\n";
        echo "Type: " . (isset($notification->type) ? $notification->type : 'N/A') . "\n";
        echo "Read at: " . ($notification->read_at ? $notification->read_at : 'Not read yet') . "\n";
        echo "Created at: " . (isset($notification->created_at) ? $notification->created_at : 'N/A') . "\n";
    }
} else {
    echo "No notifications found via Laravel relationships for this user.\n";
}

echo "\nTest completed.\n"; 