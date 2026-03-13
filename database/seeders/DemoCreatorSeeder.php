<?php

namespace Database\Seeders;

use App\Models\CreatorProfile;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoCreatorSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::firstOrCreate(
            ['email' => 'creator@example.com'],
            [
                'name' => 'Demo Creator',
                'username' => 'democreator',
                'password' => Hash::make('password'),
                'role' => 'creator',
                'is_creator' => true,
                'is_active' => true,
                'creator_approved_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        $profile = CreatorProfile::updateOrCreate(
            ['user_id' => $creator->id],
            [
                'display_name' => 'Demo Creator',
                'slug' => 'demo-creator',
                'bio' => 'This is a demo creator profile for development and testing.',
                'monthly_price' => 9.99,
                'allow_tips' => true,
                'is_published' => true,
            ]
        );

        for ($i = 1; $i <= 6; $i++) {
            $post = Post::create([
                'user_id' => $creator->id,
                'caption' => 'Demo post #' . $i . ' with sample premium content preview.',
                'is_locked' => $i % 2 === 0,
                'is_published' => true,
                'published_at' => now()->subDays($i),
            ]);

            PostMedia::create([
                'post_id' => $post->id,
                'file_path' => 'demo/demo-post-' . $i . '.jpg',
                'mime_type' => 'image/jpeg',
                'media_type' => 'image',
                'sort_order' => 0,
            ]);
        }

        User::firstOrCreate(
            ['email' => 'fan@example.com'],
            [
                'name' => 'Demo Fan',
                'username' => 'demofan',
                'password' => Hash::make('password'),
                'role' => 'fan',
                'is_creator' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
