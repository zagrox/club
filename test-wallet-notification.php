<?php

// This script tests wallet notifications

// Bootstrap the application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Log;

// Find an existing user
$user = User::first();

if (!$user) {
    echo "No user found. Please create a user first.\n";
    exit;
}

echo "Testing wallet notifications for user: {$user->name} ({$user->email})\n";

try {
    // Create a deposit transaction
    $transaction = $user->deposit(50000, 'Test deposit for notification');
    echo "Deposit transaction created: ID {$transaction->id}, Amount: {$transaction->amount}\n";
    echo "A notification should have been created in the database_notifications table.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    Log::error("Wallet notification test error", ['error' => $e->getMessage()]);
}

echo "Test completed.\n"; 