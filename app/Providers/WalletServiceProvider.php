<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Bavix\Wallet\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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

        // Ensure users have wallets when they view wallet page
        try {
            if ($this->app->runningInConsole()) {
                return;
            }
            
            $this->app->booted(function () {
                // Make sure authenticated users have a wallet
                \Illuminate\Support\Facades\Auth::check() && tap(\Illuminate\Support\Facades\Auth::user(), function ($user) {
                    if ($user && !method_exists($user, 'wallet')) {
                        Log::warning('User model does not have wallet method', ['user_id' => $user->id]);
                        return;
                    }
                    
                    if ($user && !$user->hasWallet()) {
                        $user->createWallet([
                            'name' => 'Default Wallet',
                            'slug' => 'default',
                            'description' => 'Default user wallet',
                        ]);
                    }
                });
            });
        } catch (\Exception $e) {
            Log::error('Error in WalletServiceProvider', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 