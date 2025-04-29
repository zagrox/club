<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'scheduled' => '<span class="badge bg-info">Scheduled</span>',
            'sent' => '<span class="badge bg-success">Sent</span>',
            'archived' => '<span class="badge bg-dark">Archived</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
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
        return $query->where('is_draft', false)
            ->whereNotNull('sent_at');
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true)
            ->whereNull('sent_at');
    }

    public function scopePending($query)
    {
        return $query->where('is_draft', false)
            ->whereNull('sent_at');
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
} 