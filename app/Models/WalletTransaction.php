<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wallet_id',
        'payment_id',
        'amount',
        'type',
        'description',
        'metadata',
        'balance_after',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the wallet that this transaction belongs to.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the payment associated with this transaction (if any).
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the user that owns this transaction.
     */
    public function user()
    {
        return $this->wallet->user();
    }

    /**
     * Scope a query to only include transactions of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include service-related transactions.
     */
    public function scopeForService($query, $serviceName)
    {
        return $query->where('metadata->service', $serviceName);
    }

    /**
     * Check if this transaction is a deposit.
     */
    public function isDeposit()
    {
        return $this->type === 'deposit';
    }

    /**
     * Check if this transaction is a withdrawal.
     */
    public function isWithdrawal()
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Check if this transaction is for a specific service.
     */
    public function isForService($serviceName)
    {
        return isset($this->metadata['service']) && $this->metadata['service'] === $serviceName;
    }

    /**
     * Get formatted amount with sign.
     */
    public function getFormattedAmountAttribute()
    {
        return ($this->amount >= 0 ? '+' : '') . number_format($this->amount, 2);
    }

    /**
     * Get summary of the transaction for display.
     */
    public function getSummaryAttribute()
    {
        if (isset($this->metadata['service'])) {
            $service = ucfirst($this->metadata['service']) . ' service';
            $count = $this->metadata['count'] ?? 0;
            $unit = $this->metadata['service'] === 'email' ? 'email' : 'message';
            
            if ($count > 1) {
                $unit = $unit === 'email' ? 'emails' : 'messages';
            }
            
            $campaign = isset($this->metadata['campaign']) ? " (Campaign: {$this->metadata['campaign']})" : "";
            
            return "{$service} - {$count} {$unit}{$campaign}";
        }
        
        return $this->description;
    }
} 