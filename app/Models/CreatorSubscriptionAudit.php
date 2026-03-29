<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorSubscriptionAudit extends Model
{
    protected $fillable = [
        'creator_platform_subscription_id',
        'user_id',
        'admin_user_id',
        'action',
        'note',
        'old_values',
        'new_values',
        'meta',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'meta' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(CreatorPlatformSubscription::class, 'creator_platform_subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
