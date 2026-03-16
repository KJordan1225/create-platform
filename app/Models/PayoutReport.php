<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'period_start',
        'period_end',
        'gross_subscription_revenue',
        'gross_tip_revenue',
        'gross_total',
        'platform_fee_total',
        'estimated_processor_fee_total',
        'net_creator_amount',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_subscription_revenue' => 'decimal:2',
            'gross_tip_revenue' => 'decimal:2',
            'gross_total' => 'decimal:2',
            'platform_fee_total' => 'decimal:2',
            'estimated_processor_fee_total' => 'decimal:2',
            'net_creator_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
