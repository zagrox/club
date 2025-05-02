<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'gateway',
        'ref_id',
        'track_id',
        'order_id',
        'status',
        'payment_date',
        'description',
        'metadata',
        'card_number',
        'card_hash',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'payment_date' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'verified' => 'تایید شده',
            'failed' => 'ناموفق',
            'canceled' => 'لغو شده',
            'refunded' => 'بازگشت وجه',
        ];

        return $statusLabels[$this->status] ?? 'نامشخص';
    }

    /**
     * Scope a query to only include payments of a given status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include payments by specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the order associated with this payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
} 