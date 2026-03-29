<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeWebhookEvent extends Model
{
    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'http_status',
        'processed',
        'message',
        'headers',
        'payload',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'processed' => 'boolean',
        'headers' => 'array',
        'payload' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
