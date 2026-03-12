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
            ->with([
                'user',
                'user.posts' => function ($query) {
                    $query->where('is_published', true)
                        ->latest()
                        ->with('media');
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
