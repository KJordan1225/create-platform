<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'fan_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function fan()
    {
        return $this->belongsTo(User::class, 'fan_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function latestMessages()
    {
        return $this->hasMany(Message::class)->latest()->limit(20);
    }

    public function otherParticipant(User $user): ?User
    {
        if ($user->id === $this->creator_id) {
            return $this->fan;
        }

        if ($user->id === $this->fan_id) {
            return $this->creator;
        }

        return null;
    }

    public function unreadCountFor(User $user): int
    {
        return $this->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->count();
    }
}