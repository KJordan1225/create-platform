<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plf_subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'application_fee_percent' => 'decimal:2',
        'subscribed_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function fan()
    {
        return $this->belongsTo(User::class, 'fan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && (is_null($this->ends_at) || $this->ends_at->isFuture());
    }

    public function getCreatorNetAmountAttribute(): float
    {
        return round((float) $this->amount * 0.80, 2);
    }
    
}
