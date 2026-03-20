<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;

    protected $fillable = [
        'fan_id',
        'creator_id',
        'amount',
        'currency',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_account_destination',
        'application_fee_amount',
        'application_fee_percent',
        'status',
        'message',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'application_fee_percent' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function fan()
    {
        return $this->belongsTo(User::class, 'fan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getCreatorNetAmountAttribute(): float
    {
        return (float) $this->amount - ((float) $this->application_fee_amount / 100);
    }
}
