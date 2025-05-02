<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

echo "Querying wallet transactions...\n\n";

// Get all transactions
$transactions = WalletTransaction::with('wallet')->get();

if ($transactions->isEmpty()) {
    echo "No wallet transactions found in the database.\n";
} else {
    echo "Found " . $transactions->count() . " wallet transactions:\n";
    echo "----------------------------------------------------------------\n";
    echo sprintf("%-5s %-10s %-10s %-10s %-15s %-30s %-15s\n", 
                "ID", "Wallet ID", "Amount", "Type", "Status", "Description", "Created At");
    echo "----------------------------------------------------------------\n";
    
    foreach ($transactions as $tx) {
        echo sprintf("%-5s %-10s %-10s %-10s %-15s %-30s %-15s\n", 
                    $tx->id, 
                    $tx->wallet_id, 
                    $tx->amount, 
                    $tx->type, 
                    $tx->status, 
                    substr($tx->description, 0, 30),
                    $tx->created_at->format('Y-m-d H:i:s'));
    }
    
    echo "----------------------------------------------------------------\n";
}

// Query wallet balances
$wallets = DB::table('wallets')
    ->join('users', function($join) {
        $join->on('wallets.holder_id', '=', 'users.id')
            ->where('wallets.holder_type', '=', 'App\\Models\\User');
    })
    ->select('wallets.id', 'users.name', 'users.email', 'wallets.balance')
    ->get();

echo "\nWallet Balances:\n";
echo "----------------------------------------------------------------\n";
echo sprintf("%-5s %-25s %-30s %-10s\n", 
            "ID", "User Name", "Email", "Balance");
echo "----------------------------------------------------------------\n";

foreach ($wallets as $wallet) {
    echo sprintf("%-5s %-25s %-30s %-10s\n", 
                $wallet->id, 
                substr($wallet->name, 0, 25), 
                $wallet->email, 
                $wallet->balance);
}

echo "----------------------------------------------------------------\n";

echo "\nChecking Laravel log file...\n";
// Get the last 10 lines from the latest log file
$logPath = storage_path('logs');
$logFiles = glob($logPath . '/laravel-*.log');
if (empty($logFiles)) {
    echo "No log files found in {$logPath}\n";
} else {
    rsort($logFiles); // Sort in descending order to get the latest file
    $latestLog = $logFiles[0];
    echo "Latest log file: " . basename($latestLog) . "\n";
    
    // Get the last 10 lines that contain "wallet" (case insensitive)
    $logContent = file_get_contents($latestLog);
    $lines = explode("\n", $logContent);
    $walletLines = array_filter($lines, function($line) {
        return stripos($line, 'wallet') !== false;
    });
    
    $lastWalletLines = array_slice($walletLines, -10);
    
    if (empty($lastWalletLines)) {
        echo "No wallet-related log entries found in the latest log file.\n";
    } else {
        echo "Last " . count($lastWalletLines) . " wallet-related log entries:\n";
        echo "----------------------------------------------------------------\n";
        foreach ($lastWalletLines as $line) {
            echo wordwrap($line, 100, "\n    ") . "\n\n";
        }
    }
} 