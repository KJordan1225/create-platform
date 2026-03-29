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
        'is_trial',
        'trial_ends_at',
        'starts_at',
        'renews_at',
        'ends_at',
        'canceled_at',
        'revoked_at',
        'assigned_by',
        'admin_note',
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

    public function isActive(): bool
    {
        if ($this->revoked_at) {
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
}
