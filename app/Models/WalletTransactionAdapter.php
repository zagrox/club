<?php

namespace App\Models;

use Bavix\Wallet\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Adapter class to make Bavix Wallet transactions compatible with our custom wallet transaction interface
 */
class WalletTransactionAdapter extends Model
{
    /**
     * Adapt a bavix transaction to match our old format
     * 
     * @param Transaction $transaction
     * @return \stdClass
     */
    public function adapt(Transaction $transaction)
    {
        $adapted = new \stdClass();
        
        // Basic transaction data
        $adapted->id = $transaction->id;
        $adapted->type = $transaction->type;
        
        // Convert back to dollars/tomans
        $adapted->amount = $transaction->type === 'withdraw' 
            ? -($transaction->amount / 100) 
            : ($transaction->amount / 100);
            
        // Extract description, metadata, payment_id
        $meta = $transaction->meta ?? [];
        $adapted->description = $meta['description'] ?? ($transaction->type === 'deposit' ? __('wallet.deposit') : __('wallet.withdrawal'));
        $adapted->metadata = isset($meta['metadata']) ? $meta['metadata'] : $meta;
        $adapted->payment_id = $meta['payment_id'] ?? null;
        
        // Calculate balance after (not stored in bavix transactions)
        $wallet = $transaction->wallet;
        $adapted->balance_after = $wallet ? ($wallet->balance / 100) : 0;
        
        // Status
        $adapted->status = $transaction->confirmed ? 'completed' : 'pending';
        
        // Timestamps - ensure they're Carbon instances for compatibility
        $adapted->created_at = $transaction->created_at instanceof Carbon ? 
            $transaction->created_at : 
            new Carbon($transaction->created_at);
            
        $adapted->updated_at = $transaction->updated_at instanceof Carbon ? 
            $transaction->updated_at : 
            new Carbon($transaction->updated_at);
        
        return $adapted;
    }
    
    /**
     * Adapt multiple bavix transactions
     * 
     * @param \Illuminate\Support\Collection $transactions
     * @return \Illuminate\Support\Collection
     */
    public static function adaptMany($transactions)
    {
        $adapter = new self();
        return $transactions->map(function ($transaction) use ($adapter) {
            return $adapter->adapt($transaction);
        });
    }
    
    /**
     * Format amount with plus/minus sign
     * 
     * @param Transaction $transaction
     * @return string
     */
    public static function formattedAmount(Transaction $transaction)
    {
        $amount = $transaction->type === 'withdraw' 
            ? -($transaction->amount / 100) 
            : ($transaction->amount / 100);
            
        return ($amount >= 0 ? '+' : '') . number_format($amount, 2);
    }
    
    /**
     * Get transaction summary for display
     * 
     * @param Transaction $transaction
     * @return string
     */
    public static function summary(Transaction $transaction)
    {
        $meta = $transaction->meta ?? [];
        
        if (isset($meta['description'])) {
            return $meta['description'];
        }
        
        if (isset($meta['service'])) {
            $service = ucfirst($meta['service']) . ' service';
            $count = $meta['count'] ?? 0;
            $unit = $meta['service'] === 'email' ? 'email' : 'message';
            
            if ($count > 1) {
                $unit = $unit === 'email' ? 'emails' : 'messages';
            }
            
            $campaign = isset($meta['campaign']) ? " (Campaign: {$meta['campaign']})" : "";
            
            return "{$service} - {$count} {$unit}{$campaign}";
        }
        
        return $transaction->type === 'deposit' ? __('wallet.deposit') : __('wallet.withdrawal');
    }
} 