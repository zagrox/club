<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'subtotal_amount',
        'payment_status',
        'delivery_status',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment status badge HTML.
     */
    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'Pending' => '<span class="badge bg-label-warning">Pending</span>',
            'Paid' => '<span class="badge bg-label-success">Paid</span>',
            'Failed' => '<span class="badge bg-label-danger">Failed</span>',
            'Refunded' => '<span class="badge bg-label-info">Refunded</span>',
            'Cancelled' => '<span class="badge bg-label-secondary">Cancelled</span>',
            default => '<span class="badge bg-label-primary">' . $this->payment_status . '</span>',
        };
    }

    /**
     * Get the delivery status badge HTML.
     */
    public function getDeliveryBadgeAttribute(): string
    {
        return match ($this->delivery_status) {
            'Pending' => '<span class="badge bg-label-warning">Pending</span>',
            'Processing' => '<span class="badge bg-label-info">Processing</span>',
            'Shipped' => '<span class="badge bg-label-primary">Shipped</span>',
            'Delivered' => '<span class="badge bg-label-success">Delivered</span>',
            'Cancelled' => '<span class="badge bg-label-secondary">Cancelled</span>',
            'Out for Delivery' => '<span class="badge bg-label-info">Out for Delivery</span>',
            'Ready to Pickup' => '<span class="badge bg-label-info">Ready to Pickup</span>',
            'Dispatched' => '<span class="badge bg-label-warning">Dispatched</span>',
            default => '<span class="badge bg-label-dark">' . $this->delivery_status . '</span>',
        };
    }
} 