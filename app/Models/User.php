<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\External\Contracts\ExtraDtoInterface;

class User extends Authenticatable implements MustVerifyEmail, Wallet, Customer
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasWallet, CanPay;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the custom notifications for the user.
     */
    public function customNotifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_recipients')
            ->withPivot('read_at', 'dismissed_at')
            ->withTimestamps();
    }

    /**
     * Get the wallet notifications for the user.
     */
    public function walletNotifications()
    {
        return $this->morphMany(WalletNotification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's wallet.
     */
    public function wallet()
    {
        return $this->morphOne(config('wallet.wallet.model', \Bavix\Wallet\Models\Wallet::class), 'holder');
    }

    /**
     * Get the user's wallet transactions (interface implementation).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function walletTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(config('wallet.transaction.model', \Bavix\Wallet\Models\Transaction::class), 'payable_id')
            ->where('payable_type', $this->getMorphClass());
    }

    /**
     * Custom helper to get wallet transactions through wallet.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function walletTransactionsThrough()
    {
        return $this->hasManyThrough(
            config('wallet.transaction.model', \Bavix\Wallet\Models\Transaction::class),
            config('wallet.wallet.model', \Bavix\Wallet\Models\Wallet::class),
            'holder_id',
            'wallet_id',
            'id',
            'id'
        )->where('holder_type', $this->getMorphClass());
    }

    /**
     * Create a wallet for the user if it doesn't exist.
     */
    public function getOrCreateWallet()
    {
        if (!$this->hasWallet()) {
            $this->createWallet([
                'name' => 'Default Wallet',
                'slug' => 'default',
                'description' => 'Default user wallet',
            ]);
        }
        
        return $this->wallet;
    }

    /**
     * Deposit money to user's wallet (interface implementation).
     *
     * @param string|int $amount
     * @param array|null $meta
     * @param bool $confirmed
     * @return \Bavix\Wallet\Models\Transaction
     */
    public function deposit($amount, ?array $meta = null, bool $confirmed = true): \Bavix\Wallet\Models\Transaction
    {
        return $this->getOrCreateWallet()->deposit($amount, $meta, $confirmed);
    }

    /**
     * Custom helper for deposit with description.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return \Bavix\Wallet\Models\Transaction
     */
    public function depositWithDescription($amount, $description = 'Deposit', $metadata = [])
    {
        $wallet = $this->getOrCreateWallet();
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($amount * 100);
        return $wallet->deposit($intAmount, [
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    /**
     * Withdraw money from user's wallet (interface implementation).
     *
     * @param string|int $amount
     * @param array|null $meta
     * @param bool $confirmed
     * @return \Bavix\Wallet\Models\Transaction
     */
    public function withdraw($amount, ?array $meta = null, bool $confirmed = true): \Bavix\Wallet\Models\Transaction
    {
        return $this->getOrCreateWallet()->withdraw($amount, $meta, $confirmed);
    }

    /**
     * Custom helper for withdraw with description.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return \Bavix\Wallet\Models\Transaction
     */
    public function withdrawWithDescription($amount, $description = 'Withdrawal', $metadata = [])
    {
        $wallet = $this->getOrCreateWallet();
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($amount * 100);
        return $wallet->withdraw($intAmount, [
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    /**
     * Check if user can withdraw a specific amount (interface implementation).
     *
     * @param string|int $amount
     * @param bool $allowZero
     * @return bool
     */
    public function canWithdraw($amount, bool $allowZero = false): bool
    {
        return $this->getOrCreateWallet()->canWithdraw($amount, $allowZero);
    }

    /**
     * Custom helper for checking withdrawal with float amount.
     *
     * @param float $amount
     * @param bool $allowZero
     * @return bool
     */
    public function canWithdrawAmount($amount, bool $allowZero = false)
    {
        $wallet = $this->getOrCreateWallet();
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($amount * 100);
        return $wallet->canWithdraw($intAmount, $allowZero);
    }

    /**
     * Deduct credits for email sending.
     *
     * @param int $count
     * @param string|null $campaign
     * @return \Bavix\Wallet\Models\Transaction
     * @throws \Exception
     */
    public function deductEmailCredits($count = 1, $campaign = null)
    {
        $wallet = $this->getOrCreateWallet();
        $costPerEmail = config('services.email.credit_cost', 1);
        $totalCost = $count * $costPerEmail;
        
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($totalCost * 100);
        
        return $wallet->withdraw($intAmount, [
            'description' => "Email sending service - {$count} " . ($count == 1 ? 'email' : 'emails') . 
                ($campaign ? " (Campaign: {$campaign})" : ""),
            'service' => 'email',
            'count' => $count,
            'cost_per_unit' => $costPerEmail,
            'campaign' => $campaign
        ]);
    }

    /**
     * Deduct credits for SMS sending.
     *
     * @param int $count
     * @param string|null $campaign
     * @return \Bavix\Wallet\Models\Transaction
     * @throws \Exception
     */
    public function deductSmsCredits($count = 1, $campaign = null)
    {
        $wallet = $this->getOrCreateWallet();
        $costPerSms = config('services.sms.credit_cost', 2);
        $totalCost = $count * $costPerSms;
        
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($totalCost * 100);
        
        return $wallet->withdraw($intAmount, [
            'description' => "SMS service - {$count} " . ($count == 1 ? 'message' : 'messages') . 
                ($campaign ? " (Campaign: {$campaign})" : ""),
            'service' => 'sms',
            'count' => $count,
            'cost_per_unit' => $costPerSms,
            'campaign' => $campaign
        ]);
    }

    /**
     * Check if user has enough credits for a service.
     *
     * @param string $service Service name (email, sms, etc.)
     * @param int $count Number of units
     * @return bool
     */
    public function hasEnoughCreditsFor($service, $count = 1)
    {
        $wallet = $this->getOrCreateWallet();
        $costPerUnit = config("services.{$service}.credit_cost", 0);
        $totalCost = $count * $costPerUnit;
        
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($totalCost * 100);
        
        return $wallet->canWithdraw($intAmount);
    }

    /**
     * Transfer funds to another wallet (interface implementation).
     *
     * @param \Bavix\Wallet\Interfaces\Wallet $wallet
     * @param string|int $amount
     * @param \Bavix\Wallet\External\Contracts\ExtraDtoInterface|array|null $meta
     * @return \Bavix\Wallet\Models\Transfer
     */
    public function transfer(\Bavix\Wallet\Interfaces\Wallet $wallet, $amount, \Bavix\Wallet\External\Contracts\ExtraDtoInterface|array|null $meta = null): \Bavix\Wallet\Models\Transfer
    {
        return $this->getOrCreateWallet()->transfer($wallet, $amount, $meta);
    }

    /**
     * Custom helper for transferring funds to another user.
     *
     * @param User $recipient
     * @param float $amount
     * @param string $description
     * @return array Array containing both sender and recipient transactions
     * @throws \Exception
     */
    public function transferToUser(User $recipient, $amount, $description = 'Transfer')
    {
        // Convert to integer amount for bavix/wallet
        $intAmount = (int)($amount * 100);
        
        $transfer = $this->getOrCreateWallet()->transfer(
            $recipient->getOrCreateWallet(), 
            $intAmount, 
            [
                'description' => $description,
                'from' => $this->email,
                'to' => $recipient->email
            ]
        );
        
        return [
            'sender_transaction' => $transfer->withdraw,
            'recipient_transaction' => $transfer->deposit
        ];
    }

    /**
     * Get human readable wallet balance
     * 
     * @return string
     */
    public function getFormattedBalanceAttribute()
    {
        if (!$this->hasWallet()) {
            return '0.00';
        }
        
        // Bavix wallet stores amounts as integers
        return number_format($this->wallet->balance / 100, 2);
    }

    /**
     * Check if the user has a wallet.
     * 
     * @return bool
     */
    public function hasWallet()
    {
        return !is_null($this->wallet);
    }
}
