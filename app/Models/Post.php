<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'caption',
        'is_locked',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)
            ->where('is_visible', true)
            ->latest();
    }
}
