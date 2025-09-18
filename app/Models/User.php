<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
        'banned_at',
        'ban_reason',
        'ban_expires_at',
        'status',
        'last_login_at',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
            'ban_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'user_id'); 
    }

    /**
     * Get the conversations for the user.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get the messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is banned.
     */
    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    /**
     * Get the broadcasts for the user.
     */
    public function broadcasts()
    {
        return $this->hasMany(Broadcast::class, 'created_by');
    }

    /**
     * Get the gallery items for the user.
     */
    public function galleryItems()
    {
        return $this->hasMany(UserGallery::class);
    }

    /**
     * Get unread broadcasts for the user.
     */
    public function unreadBroadcasts()
    {
        return Broadcast::forUser($this)->unreadByUser($this);
    }

    /**
     * Ban the user.
     */
    public function ban(string $reason = null): bool
    {
        return $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason ?? 'Banned by admin'
        ]);
    }

    /**
     * Unban the user.
     */
    public function unban(): bool
    {
        return $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null
        ]);
    }
}
