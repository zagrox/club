<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "Starting wallet test...\n";

// Get a user (first admin user)
$user = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->first();

if (!$user) {
    echo "No admin user found. Creating a test user...\n";
    $user = User::factory()->create([
        'email' => 'test_'.time().'@example.com',
        'name' => 'Test User'
    ]);
    $user->assignRole('admin');
}

echo "Using user: {$user->name} (ID: {$user->id})\n";

// Make sure the user has a wallet
$wallet = $user->getOrCreateWallet();
$balanceBefore = $wallet->balance;

echo "Wallet initial balance: {$balanceBefore}\n";

// Make a deposit
$depositAmount = 100;
try {
    $transaction = $user->deposit($depositAmount, 'Test deposit via script');
    echo "Deposit successful. Transaction ID: {$transaction->id}\n";
    
    // Refresh wallet to see updated balance
    $wallet->refresh();
    echo "Wallet balance after deposit: {$wallet->balance}\n";
    
    // Check if the transaction was properly recorded
    $latestTransaction = DB::table('wallet_transactions')
        ->where('wallet_id', $wallet->id)
        ->orderBy('id', 'desc')
        ->first();
    
    if ($latestTransaction) {
        echo "Transaction successfully recorded in database:\n";
        echo "  ID: {$latestTransaction->id}\n";
        echo "  Amount: {$latestTransaction->amount}\n";
        echo "  Type: {$latestTransaction->type}\n";
        echo "  Description: {$latestTransaction->description}\n";
        echo "  Balance after: {$latestTransaction->balance_after}\n";
        echo "  Created at: {$latestTransaction->created_at}\n";
    } else {
        echo "ERROR: Transaction not found in database!\n";
    }
    
    // Check logs
    echo "\nChecking logs for transaction...\n";
    Log::info('Wallet test completed successfully', [
        'user_id' => $user->id,
        'transaction_id' => $transaction->id,
        'amount' => $depositAmount
    ]);
    
    echo "Test completed successfully. Check the Laravel log for entries.\n";
    
} catch (\Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
} 