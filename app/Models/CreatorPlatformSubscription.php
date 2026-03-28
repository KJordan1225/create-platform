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
        'ends_at',
        'renews_at',
        'canceled_at',
        'meta',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'renews_at' => 'datetime',
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
        if (!in_array($this->status, ['active', 'trialing'])) {
            return false;
        }

        if ($this->ends_at && now()->greaterThan($this->ends_at)) {
            return false;
        }

        return true;
    }
}
