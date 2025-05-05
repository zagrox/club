<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Bavix\Wallet\Models\Wallet;
use App\Models\User;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for transaction events
        Event::listen('wallet.transaction.created', function (Transaction $transaction) {
            // Log or process transaction creation
            \Log::info('New wallet transaction created', [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'wallet_id' => $transaction->wallet_id,
            ]);
        });

        // Listen for transfer events
        Event::listen('wallet.transfer.created', function (Transfer $transfer) {
            // Log or process transfer creation
            \Log::info('New wallet transfer created', [
                'id' => $transfer->id,
                'deposit_id' => $transfer->deposit_id,
                'withdraw_id' => $transfer->withdraw_id,
                'status' => $transfer->status,
            ]);
        });
    }
} 