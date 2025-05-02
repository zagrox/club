<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Payment;
use App\Services\ZibalPayment;
use App\Services\MockZibalPayment;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

echo "Testing payment callback process...\n";

// 1. Create a user for testing
$user = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->first();

echo "Using user: {$user->name} (ID: {$user->id})\n";

// 2. Create a mock payment
$walletBalanceBefore = $user->getOrCreateWallet()->balance;
echo "Wallet balance before payment: {$walletBalanceBefore}\n";

$paymentAmount = 50000; // 50,000 Rials (500 Tomans)
$payment = new Payment([
    'user_id' => $user->id,
    'amount' => $paymentAmount,
    'gateway' => 'zibal',
    'status' => 'pending',
    'description' => 'Test wallet deposit',
    'metadata' => ['type' => 'wallet_deposit'],
]);
$payment->save();
echo "Created payment record with ID: {$payment->id}\n";

// 3. Simulate a payment request
$mockZibal = new MockZibalPayment();
$callbackUrl = route('wallet.deposit.callback');
$response = $mockZibal->request(
    $payment->amount,
    $callbackUrl,
    $payment->id,
    $payment->description,
    ['payment_id' => $payment->id, 'type' => 'wallet_deposit']
);

if (!$response || !isset($response['trackId'])) {
    echo "ERROR: Failed to get trackId from mock payment request.\n";
    exit(1);
}

$trackId = $response['trackId'];
echo "Mock payment request successful. Track ID: {$trackId}\n";

// 4. Update payment with track ID
$payment->track_id = $trackId;
$payment->save();
echo "Updated payment with track ID\n";

// 5. Simulate callback from gateway
echo "Simulating payment callback with success=1...\n";

// Create a mock request
$request = Request::create(
    '/wallet/deposit/callback',
    'GET',
    [
        'trackId' => $trackId,
        'success' => 1,
        'orderId' => $payment->id
    ]
);

// Manually call the controller method
$controller = new WalletController();
$response = $controller->depositCallback($request);

// Check if callback was processed
$payment->refresh();
echo "Payment status after callback: {$payment->status}\n";

// 6. Check if wallet balance was updated
$user->refresh();
$walletBalanceAfter = $user->wallet->balance;
echo "Wallet balance after payment: {$walletBalanceAfter}\n";

// 7. Calculate expected amount (Rials to Tomans conversion)
$expectedAmount = $paymentAmount / 10;
$depositAmount = $walletBalanceAfter - $walletBalanceBefore;
echo "Expected deposit amount: {$expectedAmount}\n";
echo "Actual deposit amount: {$depositAmount}\n";

if ($depositAmount == $expectedAmount) {
    echo "SUCCESS: Wallet was credited with the correct amount.\n";
} else {
    echo "ERROR: Wallet balance doesn't match expected amount.\n";
}

// 8. Check for wallet transaction record
$transaction = \App\Models\WalletTransaction::where('wallet_id', $user->wallet->id)
    ->orderBy('id', 'desc')
    ->first();

if ($transaction) {
    echo "\nWallet transaction details:\n";
    echo "  ID: {$transaction->id}\n";
    echo "  Type: {$transaction->type}\n";
    echo "  Amount: {$transaction->amount}\n";
    echo "  Description: {$transaction->description}\n";
    echo "  Balance after: {$transaction->balance_after}\n";
    echo "  Created at: {$transaction->created_at}\n";
    
    if ($transaction->amount == $expectedAmount) {
        echo "SUCCESS: Transaction amount is correct.\n";
    } else {
        echo "ERROR: Transaction amount doesn't match expected value.\n";
    }
} else {
    echo "ERROR: No wallet transaction record found.\n";
} 