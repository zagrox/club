<?php

// This script directly tests the wallet notification channel

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WalletTransaction;
use App\Channels\WalletDatabaseChannel;
use Illuminate\Support\Facades\Log;

class DirectWalletTestNotification extends \Illuminate\Notifications\Notification
{
    public function via($notifiable)
    {
        return [WalletDatabaseChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Test Wallet Notification',
            'message' => 'This is a direct test of the wallet notification system',
            'icon' => 'bx-wallet',
            'color' => 'primary',
            'url' => '/wallet/transactions'
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

    echo "Sending direct wallet test notification to user: {$user->name} ({$user->email})\n";

    // Send notification directly through our custom channel
    $channel = new WalletDatabaseChannel();
    $notification = new DirectWalletTestNotification();
    
    $result = $channel->send($user, $notification);
    
    echo "Notification sent successfully with ID: {$result->id}\n";
    
    // Check the wallet_notifications table for confirmation
    echo "\nChecking for confirmation in wallet_notifications table...\n";
    
    // Give it a moment to process
    sleep(1);
    
    $count = $user->walletNotifications()->count();
    
    echo "Found {$count} wallet notifications for this user.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    Log::error("Direct wallet notification test error", ['error' => $e->getMessage()]);
}

echo "\nTest completed.\n"; 