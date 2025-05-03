<?php

// This script checks for wallet notifications

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

echo "Checking wallet notifications for user: {$user->name} ({$user->email})\n";

// Check the wallet_notifications table
$notifications = DB::table('wallet_notifications')
    ->where('notifiable_type', User::class)
    ->where('notifiable_id', $user->id)
    ->get();

echo "\nNotifications from wallet_notifications table:\n";
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
    echo "No wallet notifications found for this user.\n";
}

echo "\nTest completed.\n"; 