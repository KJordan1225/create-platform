<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Billable;

    protected $guarded = [];

    protected $casts = [
        'stripe_charges_enabled' => 'boolean',
        'stripe_payouts_enabled' => 'boolean',
        'stripe_requirements' => 'array',
        'stripe_onboarded_at' => 'datetime',
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

    public function isStripeConnected(): bool
    {
        return !empty($this->stripe_account_id)
            && $this->stripe_charges_enabled
            && $this->stripe_payouts_enabled
            && $this->stripe_onboarding_status === 'connected';
    }

    public function needsStripeAction(): bool
    {
        return $this->stripe_onboarding_status === 'needs_action';
    }

    public function hasPendingStripeOnboarding(): bool
    {
        return !empty($this->stripe_account_id)
            && $this->stripe_onboarding_status === 'pending';
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

    public function payoutReports()
    {
        return $this->hasMany(PayoutReport::class, 'creator_id');
    }   

    public function receivedSubscriptions()
    {
        return $this->hasMany(Plf_subscription::class, 'creator_id');
    }

    public function sentSubscriptions()
    {
        return $this->hasMany(Plf_subscription::class, 'fan_id');
    }

    public function receivedTips()
    {
        return $this->hasMany(Tip::class, 'creator_id');
    }

    public function sentTips()
    {
        return $this->hasMany(Tip::class, 'fan_id');
    }

}
