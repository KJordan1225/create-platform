<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use Illuminate\Http\Request;

class CreatorProfileController extends Controller 
{
    public function show(Request $request, string $slug)
    {
        $profile = CreatorProfile::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereHas('user', function ($query) {
                $query->where('is_active', true)
                    ->where('is_creator', true)
                    ->whereNotNull('creator_approved_at');
            })
            ->with([
                'user',
                'user.posts' => function ($query) {
                    $query->where('is_published', true)
                        ->latest()
                        ->with(['media', 'comments.user']);
                }
            ])
            ->firstOrFail();

        $viewer = $request->user();
        $isSubscribed = $viewer
            ? $viewer->hasActiveSubscriptionTo($profile->user)
            : false;

        return view('creators.show', [
            'profile' => $profile,
            'creator' => $profile->user,
            'posts' => $profile->user->posts,
            'isSubscribed' => $isSubscribed,
        ]);
    }
}
