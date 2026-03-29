<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatorPlatformPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'interval',
        'stripe_price_id',
        'is_active',
        'has_trial',
        'trial_days',
        'description',
        'features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_trial' => 'boolean',
        'features' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CreatorPlatformSubscription::class);
    }

    public function getPriceDisplayAttribute(): string
    {
        return '$' . number_format($this->price / 100, 2) . '/' . $this->interval;
    }

    public function getTrialDisplayAttribute(): ?string
    {
        if (!$this->has_trial || $this->trial_days < 1) {
            return null;
        }

        return $this->trial_days . ' day free trial';
    }
}
