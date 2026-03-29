<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPlatformSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'creator_platform_plan_id',
        'provider',
        'stripe_checkout_session_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'status',
        'starts_at',
        'renews_at',
        'ends_at',
        'canceled_at',
        'meta',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'renews_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(CreatorPlatformPlan::class, 'creator_platform_plan_id');
    }

    public function isActive(): bool
    {
        if (!in_array($this->status, ['active', 'trialing'], true)) {
            return false;
        }

        if ($this->ends_at && now()->greaterThan($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function isCanceledButStillActive(): bool
    {
        return $this->isActive()
            && $this->ends_at !== null
            && $this->ends_at->isFuture();
    }

    public function isFullyCanceled(): bool
    {
        return in_array($this->status, ['canceled', 'unpaid', 'inactive'], true)
            || ($this->ends_at && $this->ends_at->isPast());
    }

    public function willCancelAtPeriodEnd(): bool
    {
        return (bool) data_get($this->meta, 'cancel_at_period_end', false);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'active', 'trialing' => 'success',
            'past_due' => 'warning',
            'canceled', 'unpaid', 'inactive' => 'danger',
            default => 'secondary',
        };
    }
}
