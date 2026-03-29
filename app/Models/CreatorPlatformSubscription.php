<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_trial',
        'trial_ends_at',
        'starts_at',
        'renews_at',
        'ends_at',
        'canceled_at',
        'revoked_at',
        'assigned_by',
        'admin_note',
        'trial_ending_notice_sent_at',
        'cancel_scheduled_notice_sent_at',
        'revoked_notice_sent_at',
        'expired_at',
        'meta',
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'renews_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'revoked_at' => 'datetime',
        'trial_ending_notice_sent_at' => 'datetime',
        'cancel_scheduled_notice_sent_at' => 'datetime',
        'revoked_notice_sent_at' => 'datetime',
        'expired_at' => 'datetime',
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

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(CreatorSubscriptionAudit::class);
    }

    public function isActive(): bool
    {
        if ($this->revoked_at || $this->expired_at) {
            return false;
        }

        if (!in_array($this->status, ['active', 'trialing'], true)) {
            return false;
        }

        if ($this->trial_ends_at && $this->status === 'trialing' && now()->greaterThan($this->trial_ends_at)) {
            return false;
        }

        if ($this->ends_at && now()->greaterThan($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function willCancelAtPeriodEnd(): bool
    {
        return (bool) data_get($this->meta, 'cancel_at_period_end', false);
    }

    public function isManual(): bool
    {
        return blank($this->stripe_subscription_id);
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

    public function isTrialEndingSoon(int $days = 3): bool
    {
        return $this->status === 'trialing'
            && $this->trial_ends_at
            && now()->lte($this->trial_ends_at)
            && now()->diffInDays($this->trial_ends_at, false) <= $days;
    }
}
