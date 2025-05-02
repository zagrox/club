<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'holder_id',
        'holder_type',
        'name',
        'slug',
        'uuid',
        'balance',
        'decimal_places',
        'meta',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'meta' => 'array',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'holder_id')->where('holder_type', User::class);
    }

    /**
     * Get the wallet transactions.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Deposit funds to the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return WalletTransaction
     */
    public function deposit($amount, $description = 'Deposit', $metadata = [])
    {
        // Validate amount
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        // Update wallet balance
        $this->balance += $amount;
        $this->save();

        // Create transaction record
        $transaction = new WalletTransaction([
            'wallet_id' => $this->id,
            'amount' => $amount,
            'type' => 'deposit',
            'description' => $description,
            'metadata' => $metadata,
            'balance_after' => $this->balance,
        ]);
        $transaction->save();

        Log::info('Wallet deposit', [
            'holder_id' => $this->holder_id,
            'amount' => $amount,
            'balance' => $this->balance,
            'description' => $description
        ]);

        return $transaction;
    }

    /**
     * Withdraw funds from the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return WalletTransaction
     * @throws \Exception
     */
    public function withdraw($amount, $description = 'Withdrawal', $metadata = [])
    {
        // Validate amount
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero');
        }

        // Check if sufficient balance
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient funds in wallet');
        }

        // Update wallet balance
        $this->balance -= $amount;
        $this->save();

        // Create transaction record
        $transaction = new WalletTransaction([
            'wallet_id' => $this->id,
            'amount' => -$amount, // Negative to indicate withdrawal
            'type' => 'withdrawal',
            'description' => $description,
            'metadata' => $metadata,
            'balance_after' => $this->balance,
        ]);
        $transaction->save();

        Log::info('Wallet withdrawal', [
            'holder_id' => $this->holder_id,
            'amount' => $amount,
            'balance' => $this->balance,
            'description' => $description
        ]);

        return $transaction;
    }

    /**
     * Check if the wallet has sufficient funds for withdrawal.
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientFunds($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Deduct credits for email sending service.
     *
     * @param int $count Number of emails to send
     * @param string $campaign Optional campaign identifier
     * @return WalletTransaction
     * @throws \Exception
     */
    public function deductCreditsForEmail($count = 1, $campaign = null)
    {
        $costPerEmail = config('services.email.credit_cost', 1);
        $totalCost = $count * $costPerEmail;
        
        $metadata = [
            'service' => 'email',
            'count' => $count,
            'cost_per_unit' => $costPerEmail,
            'campaign' => $campaign
        ];
        
        $description = "Email sending service - {$count} " . ($count == 1 ? 'email' : 'emails');
        if ($campaign) {
            $description .= " (Campaign: {$campaign})";
        }
        
        return $this->withdraw($totalCost, $description, $metadata);
    }

    /**
     * Deduct credits for SMS service.
     *
     * @param int $count Number of SMS messages
     * @param string $campaign Optional campaign identifier
     * @return WalletTransaction
     * @throws \Exception
     */
    public function deductCreditsForSms($count = 1, $campaign = null)
    {
        $costPerSms = config('services.sms.credit_cost', 2);
        $totalCost = $count * $costPerSms;
        
        $metadata = [
            'service' => 'sms',
            'count' => $count,
            'cost_per_unit' => $costPerSms,
            'campaign' => $campaign
        ];
        
        $description = "SMS service - {$count} " . ($count == 1 ? 'message' : 'messages');
        if ($campaign) {
            $description .= " (Campaign: {$campaign})";
        }
        
        return $this->withdraw($totalCost, $description, $metadata);
    }

    /**
     * Check if user can afford a service with given cost.
     *
     * @param string $service Service type (email, sms, etc.)
     * @param int $count Number of units
     * @return bool
     */
    public function canAffordService($service, $count = 1)
    {
        $costPerUnit = config("services.{$service}.credit_cost", 0);
        $totalCost = $count * $costPerUnit;
        
        return $this->hasSufficientFunds($totalCost);
    }
} 