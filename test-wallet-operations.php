<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

echo "Testing wallet operations (withdrawal and transfer)...\n\n";

// Get admin user
$user = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->first();

echo "Using admin user: {$user->name} (ID: {$user->id})\n";
$wallet = $user->getOrCreateWallet();
echo "Current wallet balance: {$wallet->balance}\n\n";

// Create a second user for testing transfers
$secondUser = User::where('id', '!=', $user->id)->first();
if (!$secondUser) {
    echo "Creating a second test user...\n";
    $secondUser = User::factory()->create([
        'name' => 'Test Transfer User',
        'email' => 'transfer_'.time().'@example.com'
    ]);
}
echo "Second user for transfer: {$secondUser->name} (ID: {$secondUser->id})\n";
$secondWallet = $secondUser->getOrCreateWallet();
echo "Second user wallet balance: {$secondWallet->balance}\n\n";

// Test 1: Withdrawal
echo "TEST 1: WITHDRAWAL\n";
echo "----------------\n";
$withdrawalAmount = 50;
$balanceBefore = $wallet->balance;

try {
    $transaction = $user->withdraw($withdrawalAmount, 'Test withdrawal operation');
    
    echo "Withdrawal successful. Transaction ID: {$transaction->id}\n";
    $wallet->refresh();
    echo "Wallet balance after withdrawal: {$wallet->balance}\n";
    
    $expectedBalance = $balanceBefore - $withdrawalAmount;
    if ($wallet->balance == $expectedBalance) {
        echo "SUCCESS: Balance correctly updated after withdrawal.\n";
    } else {
        echo "ERROR: Balance doesn't match expected amount after withdrawal.\n";
        echo "Expected: {$expectedBalance}, Actual: {$wallet->balance}\n";
    }
    
    // Check transaction record
    $tx = WalletTransaction::find($transaction->id);
    echo "\nWithdrawal transaction details:\n";
    echo "  Amount: {$tx->amount}\n";
    echo "  Type: {$tx->type}\n";
    echo "  Description: {$tx->description}\n";
    echo "  Balance after: {$tx->balance_after}\n";
    
} catch (\Exception $e) {
    echo "ERROR during withdrawal: {$e->getMessage()}\n";
}

// Test 2: Transfer
echo "\nTEST 2: TRANSFER\n";
echo "----------------\n";
$transferAmount = 25;
$userBalanceBefore = $wallet->balance;
$secondUserBalanceBefore = $secondWallet->balance;

try {
    $result = $user->transfer($secondUser, $transferAmount, 'Test transfer operation');
    
    echo "Transfer successful.\n";
    $wallet->refresh();
    $secondWallet->refresh();
    
    echo "Sender wallet balance after transfer: {$wallet->balance}\n";
    echo "Recipient wallet balance after transfer: {$secondWallet->balance}\n";
    
    $expectedSenderBalance = $userBalanceBefore - $transferAmount;
    $expectedRecipientBalance = $secondUserBalanceBefore + $transferAmount;
    
    if ($wallet->balance == $expectedSenderBalance) {
        echo "SUCCESS: Sender balance correctly updated.\n";
    } else {
        echo "ERROR: Sender balance doesn't match expected amount.\n";
        echo "Expected: {$expectedSenderBalance}, Actual: {$wallet->balance}\n";
    }
    
    if ($secondWallet->balance == $expectedRecipientBalance) {
        echo "SUCCESS: Recipient balance correctly updated.\n";
    } else {
        echo "ERROR: Recipient balance doesn't match expected amount.\n";
        echo "Expected: {$expectedRecipientBalance}, Actual: {$secondWallet->balance}\n";
    }
    
    // Check transaction records
    $senderTx = $result['sender_transaction'];
    $recipientTx = $result['recipient_transaction'];
    
    echo "\nSender transaction details:\n";
    echo "  ID: {$senderTx->id}\n";
    echo "  Amount: {$senderTx->amount}\n";
    echo "  Type: {$senderTx->type}\n";
    echo "  Description: {$senderTx->description}\n";
    echo "  Balance after: {$senderTx->balance_after}\n";
    
    echo "\nRecipient transaction details:\n";
    echo "  ID: {$recipientTx->id}\n";
    echo "  Amount: {$recipientTx->amount}\n";
    echo "  Type: {$recipientTx->type}\n";
    echo "  Description: {$recipientTx->description}\n";
    echo "  Balance after: {$recipientTx->balance_after}\n";
    
} catch (\Exception $e) {
    echo "ERROR during transfer: {$e->getMessage()}\n";
}

// Final report
echo "\nFINAL WALLET STATES:\n";
echo "--------------------\n";
$wallets = DB::table('wallets')
    ->join('users', function($join) {
        $join->on('wallets.holder_id', '=', 'users.id')
            ->where('wallets.holder_type', '=', 'App\\Models\\User');
    })
    ->select('wallets.id', 'users.name', 'users.email', 'wallets.balance')
    ->get();

foreach ($wallets as $w) {
    echo "User: {$w->name}, Balance: {$w->balance}\n";
}

echo "\nRECENT TRANSACTIONS:\n";
echo "-------------------\n";
$recentTransactions = WalletTransaction::with('wallet')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recentTransactions as $tx) {
    echo "ID: {$tx->id}, Type: {$tx->type}, Amount: {$tx->amount}, Description: {$tx->description}\n";
} 