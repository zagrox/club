<?php

// This script tests wallet deposit and notifications in one transaction

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

// Find an existing user
$user = User::first();

if (!$user) {
    echo "No user found. Please create a user first.\n";
    exit;
}

echo "Testing wallet deposit for user: {$user->name} ({$user->email})\n";

try {
    // Create a deposit transaction
    $amount = rand(1000, 10000);
    $description = "Test wallet deposit #" . time();
    
    echo "Depositing {$amount} to wallet...\n";
    
    $transaction = $user->deposit($amount, $description);
    
    echo "Transaction created: ID {$transaction->id}, Amount: {$transaction->amount}\n";
    echo "Waiting for notification processing...\n";
    
    // Give it a second to process
    sleep(2);
    
    // Check wallet notifications
    $notifications = $user->walletNotifications()
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();
    
    echo "\nLatest wallet notifications:\n";
    
    if ($notifications->count() > 0) {
        echo "Found {$notifications->count()} notifications:\n";
        
        foreach ($notifications as $notification) {
            $data = $notification->data;
            echo "-------------------------------------------\n";
            echo "ID: {$notification->id}\n";
            echo "Type: {$notification->type}\n";
            echo "Read at: " . ($notification->read_at ? $notification->read_at : 'Not read yet') . "\n";
            echo "Created at: {$notification->created_at}\n";
            
            // Check if data is already an array (Laravel may have already decoded it)
            if (is_array($data)) {
                echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            } else {
                // If it's a string, decode it
                echo "Data: " . $data . "\n";
            }
        }
    } else {
        echo "No wallet notifications found for this user.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    Log::error("Wallet deposit test error", ['error' => $e->getMessage()]);
}

echo "\nTest completed.\n"; 