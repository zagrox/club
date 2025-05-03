<?php

// This script directly tests Laravel's notification system

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class TestNotification extends \Illuminate\Notifications\Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'icon' => 'bx-test',
            'color' => 'info',
            'url' => '/test'
        ];
    }
}

try {
    // Find an existing user
    $user = User::first();

    if (!$user) {
        echo "No user found. Please create a user first.\n";
        exit;
    }

    echo "Sending direct test notification to user: {$user->name} ({$user->email})\n";

    // Send notification directly
    $user->notify(new TestNotification());
    
    echo "Notification sent successfully.\n";
    
    // Check if the notification is in the database
    echo "Checking if notification was saved in database_notifications table...\n";
    
    // Give it a moment to process
    sleep(1);
    
    $count = \Illuminate\Support\Facades\DB::table('database_notifications')
        ->where('notifiable_type', User::class)
        ->where('notifiable_id', $user->id)
        ->count();
    
    echo "Found {$count} notifications for this user in database_notifications table.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    Log::error("Direct notification test error", ['error' => $e->getMessage()]);
}

echo "\nTest completed.\n"; 