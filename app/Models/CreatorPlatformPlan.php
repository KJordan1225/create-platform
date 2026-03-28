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
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CreatorPlatformSubscription::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->price / 100, 2);
    }
}
