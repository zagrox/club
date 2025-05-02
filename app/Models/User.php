<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

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
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_recipients')
            ->withPivot('read_at', 'dismissed_at')
            ->withTimestamps();
    }

    /**
     * Get the user's wallet.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'holder_id')
            ->where('holder_type', self::class);
    }

    /**
     * Get the user's wallet transactions.
     */
    public function walletTransactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class);
    }

    /**
     * Create a wallet for the user if it doesn't exist.
     */
    public function getOrCreateWallet()
    {
        if (!$this->wallet) {
            $wallet = new Wallet([
                'holder_id' => $this->id,
                'holder_type' => self::class,
                'name' => 'Default Wallet',
                'slug' => 'default',
                'uuid' => (string) Str::uuid(),
                'balance' => 0,
                'decimal_places' => 2,
                'description' => 'Default user wallet',
            ]);
            $wallet->save();
            
            $this->refresh();
        }
        
        return $this->wallet;
    }

    /**
     * Deposit money to user's wallet.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return WalletTransaction
     */
    public function deposit($amount, $description = 'Deposit', $metadata = [])
    {
        $wallet = $this->getOrCreateWallet();
        return $wallet->deposit($amount, $description, $metadata);
    }

    /**
     * Withdraw money from user's wallet.
     *
     * @param float $amount
     * @param string $description
     * @param array $metadata
     * @return WalletTransaction
     * @throws \Exception
     */
    public function withdraw($amount, $description = 'Withdrawal', $metadata = [])
    {
        $wallet = $this->getOrCreateWallet();
        return $wallet->withdraw($amount, $description, $metadata);
    }

    /**
     * Check if user can withdraw a specific amount.
     *
     * @param float $amount
     * @return bool
     */
    public function canWithdraw($amount)
    {
        $wallet = $this->getOrCreateWallet();
        return $wallet->hasSufficientFunds($amount);
    }

    /**
     * Deduct credits for email sending.
     *
     * @param int $count
     * @param string|null $campaign
     * @return WalletTransaction
     * @throws \Exception
     */
    public function deductEmailCredits($count = 1, $campaign = null)
    {
        $wallet = $this->getOrCreateWallet();
        return $wallet->deductCreditsForEmail($count, $campaign);
    }

    /**
     * Deduct credits for SMS sending.
     *
     * @param int $count
     * @param string|null $campaign
     * @return WalletTransaction
     * @throws \Exception
     */
    public function deductSmsCredits($count = 1, $campaign = null)
    {
        $wallet = $this->getOrCreateWallet();
        return $wallet->deductCreditsForSms($count, $campaign);
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
        return $wallet->canAffordService($service, $count);
    }

    /**
     * Transfer funds to another user.
     *
     * @param User $recipient
     * @param float $amount
     * @param string $description
     * @return array Array containing both sender and recipient transactions
     * @throws \Exception
     */
    public function transfer(User $recipient, $amount, $description = 'Transfer')
    {
        // Check if user has enough funds
        if (!$this->canWithdraw($amount)) {
            throw new \Exception('Insufficient funds for transfer');
        }
        
        // Create recipient wallet if it doesn't exist
        $recipientWallet = $recipient->getOrCreateWallet();
        
        // Create withdrawal transaction for sender
        $senderTransaction = $this->withdraw($amount, "Transfer to {$recipient->email}");
        
        // Create deposit transaction for recipient
        $recipientTransaction = $recipient->deposit($amount, "Transfer from {$this->email}");
        
        return [
            'sender_transaction' => $senderTransaction,
            'recipient_transaction' => $recipientTransaction
        ];
    }
}
