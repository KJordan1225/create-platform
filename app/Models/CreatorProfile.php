<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'slug',
        'bio',
        'avatar_path',
        'banner_path',
        'monthly_price',
        'stripe_product_id',
        'stripe_price_id',
        'is_published',
        'allow_tips',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'is_published' => 'boolean',
            'allow_tips' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar_path
            ? asset('storage/' . $this->avatar_path)
            : asset('images/default-avatar.png');
    }

    public function getBannerUrlAttribute(): string
    {
        return $this->banner_path
            ? asset('storage/' . $this->banner_path)
            : asset('images/default-banner.jpg');
    }
}
