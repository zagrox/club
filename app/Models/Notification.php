<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'message',
        'priority',
        'category',
        'audience_type',
        'audience_ids',
        'delivery_methods',
        'is_scheduled',
        'scheduled_at',
        'is_draft',
        'sent_at',
        'attachments',
        'created_by',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'audience_ids' => 'array',
        'delivery_methods' => 'array',
        'is_scheduled' => 'boolean',
        'scheduled_at' => 'datetime',
        'is_draft' => 'boolean',
        'sent_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the priority badge based on the notification priority.
     *
     * @return string
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high' => '<span class="badge bg-danger">High</span>',
            'medium' => '<span class="badge bg-warning">Medium</span>',
            'low' => '<span class="badge bg-success">Low</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->priority) . '</span>',
        };
    }

    /**
     * Get the category badge based on the notification category.
     *
     * @return string
     */
    public function getCategoryBadgeAttribute(): string
    {
        return match ($this->category) {
            'system' => '<span class="badge bg-primary">System</span>',
            'update' => '<span class="badge bg-info">Update</span>',
            'reminder' => '<span class="badge bg-warning">Reminder</span>',
            'custom' => '<span class="badge bg-secondary">Custom</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->category) . '</span>',
        };
    }

    /**
     * Get the status badge based on the notification status.
     *
     * @return string
     */
    public function getStatusBadgeAttribute(): string
    {
        $statusClasses = [
            'pending' => 'bg-secondary',
            'processing' => 'bg-primary',
            'draft' => 'bg-secondary',
            'scheduled' => 'bg-info',
            'sent' => 'bg-success',
            'failed' => 'bg-danger',
            'archived' => 'bg-dark',
            'canceled' => 'bg-warning',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-secondary';
        $label = ucfirst($this->status);

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    /**
     * Get the user who created the notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the recipients of the notification.
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_recipients')
            ->withPivot('read_at', 'dismissed_at')
            ->withTimestamps();
    }

    public function getPriorityBadgeClass()
    {
        return [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
        ][$this->priority] ?? 'secondary';
    }

    public function getCategoryBadgeClass()
    {
        return [
            'system' => 'primary',
            'announcement' => 'success',
            'alert' => 'danger',
            'event' => 'info',
            'update' => 'warning',
        ][$this->category] ?? 'secondary';
    }

    public function scopeDraft($query)
    {
        return $query->where('is_draft', true);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true)
            ->whereNull('sent_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isReadBy($userId)
    {
        return $this->recipients()
            ->wherePivot('user_id', $userId)
            ->wherePivotNotNull('read_at')
            ->exists();
    }

    public function markAsRead($userId)
    {
        $this->recipients()
            ->updateExistingPivot($userId, ['read_at' => now()]);
    }

    public function markAsDismissed($userId)
    {
        $this->recipients()
            ->updateExistingPivot($userId, ['dismissed_at' => now()]);
    }

    /**
     * Get all available statuses for notifications.
     * 
     * @return array
     */
    public static function getStatuses(): array
    {
        return Config::get('notifications.statuses', [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'sent' => 'Sent',
            'failed' => 'Failed',
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'archived' => 'Archived',
            'canceled' => 'Canceled',
        ]);
    }

    /**
     * Check if the status is valid.
     *
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return array_key_exists($status, self::getStatuses());
    }
} 