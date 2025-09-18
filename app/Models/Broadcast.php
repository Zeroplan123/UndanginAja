<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'target_type',
        'target_users',
        'status',
        'scheduled_at',
        'sent_at',
        'created_by',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'target_users' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];

    /**
     * Get the user who created this broadcast.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all read records for this broadcast.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(BroadcastRead::class);
    }

    /**
     * Check if broadcast has been read by specific user.
     */
    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }

    /**
     * Mark broadcast as read by user.
     */
    public function markAsReadBy(User $user): void
    {
        $this->reads()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'read_at' => now()
        ]);
    }

    /**
     * Get target users for this broadcast.
     */
    public function getTargetUsers()
    {
        if ($this->target_type === 'all') {
            return User::where('role', '!=', 'admin')->where('is_banned', false)->get();
        }

        if ($this->target_type === 'specific' && $this->target_users) {
            return User::whereIn('id', $this->target_users)
                      ->where('is_banned', false)
                      ->get();
        }

        return collect();
    }

    /**
     * Scope for active broadcasts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sent broadcasts.
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for scheduled broadcasts.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for unread broadcasts by user.
     */
    public function scopeUnreadByUser(Builder $query, User $user): Builder
    {
        return $query->whereDoesntHave('reads', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Get broadcasts for specific user (considering targeting and user registration date).
     */
    public static function forUser(User $user)
    {
        return static::active()
            ->sent()
            ->where('sent_at', '>=', $user->created_at) // Only broadcasts sent after user registration
            ->where(function ($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhere(function ($q) use ($user) {
                          $q->where('target_type', 'specific')
                            ->whereJsonContains('target_users', $user->id);
                      });
            });
    }

    /**
     * Get type badge color.
     */
    public function getTypeBadgeColor(): string
    {
        return match($this->type) {
            'promo' => 'bg-green-100 text-green-800',
            'update' => 'bg-blue-100 text-blue-800',
            'maintenance' => 'bg-red-100 text-red-800',
            'announcement' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityBadgeColor(): string
    {
        return match($this->priority) {
            3 => 'bg-red-100 text-red-800',
            2 => 'bg-yellow-100 text-yellow-800',
            1 => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get priority text.
     */
    public function getPriorityText(): string
    {
        return match($this->priority) {
            3 => 'High',
            2 => 'Medium',
            1 => 'Low',
            default => 'Unknown'
        };
    }
}
