<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Billable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'is_creator',
        'creator_approved_at',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_creator' => 'boolean',
            'creator_approved_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function creatorProfile()
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function outgoingSubscriptions()
    {
        return $this->hasMany(Plf_subscription::class, 'fan_id');
    }

    public function incomingSubscriptions()
    {
        return $this->hasMany(Plf_subscription::class, 'creator_id');
    }

    public function tipsSent()
    {
        return $this->hasMany(Tip::class, 'fan_id');
    }

    public function tipsReceived()
    {
        return $this->hasMany(Tip::class, 'creator_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCreator(): bool
    {
        return $this->role === 'creator' && $this->is_creator;
    }

    public function isApprovedCreator(): bool
    {
        return $this->isCreator() && ! is_null($this->creator_approved_at);
    }

    public function hasActiveSubscriptionTo(User $creator): bool
    {
        return $this->outgoingSubscriptions()
            ->where('creator_id', $creator->id)
            ->where('status', 'active')
            ->exists();
    }

    public function creatorConversations()
    {
        return $this->hasMany(Conversation::class, 'creator_id');
    }

    public function fanConversations()
    {
        return $this->hasMany(Conversation::class, 'fan_id');
    }

    public function allConversations()
    {
        if ($this->isCreator()) {
            return Conversation::query()->where('creator_id', $this->id);
        }

        return Conversation::query()->where('fan_id', $this->id);
    }

    public function unreadMessagesCount(): int
    {
        $conversationIds = $this->allConversations()->pluck('id');

        return Message::query()
            ->whereIn('conversation_id', $conversationIds)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $this->id)
            ->count();
    }

}
