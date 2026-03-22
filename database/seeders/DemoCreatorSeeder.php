<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\CreatorProfile;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'is_creator' => false,
                'is_active' => true,
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
            $post = Post::firstOrCreate(
                [
                    'user_id' => $creator->id,
                    'caption' => 'Demo post #' . $i . ' with sample premium content preview.',
                ],
                [
                    'is_locked' => $i % 2 === 0,
                    'is_published' => true,
                    'published_at' => now()->subDays($i),
                ]
            );

            if ($post->media()->count() === 0) {
                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => 'demo/demo-post-' . $i . '.jpg',
                    'mime_type' => 'image/jpeg',
                    'media_type' => 'image',
                    'sort_order' => 0,
                ]);
            }
        }

        $fan = User::firstOrCreate(
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

        $conversation = Conversation::firstOrCreate(
            [
                'creator_id' => $creator->id,
                'fan_id' => $fan->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        if ($conversation->messages()->count() === 0) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $fan->id,
                'body' => 'Hi, I really enjoy your content.',
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $creator->id,
                'body' => 'Thank you so much for the support!',
                'read_at' => now(),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);
        }
    }
}
