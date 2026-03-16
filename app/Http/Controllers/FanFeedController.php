<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FanFeedController extends Controller
{
    public function __invoke(Request $request): View
    {
        $creatorIds = $request->user()
            ->outgoingSubscriptions()
            ->where('status', 'active')
            ->pluck('creator_id');

        $posts = Post::query()
            ->whereIn('user_id', $creatorIds)
            ->where('is_published', true)
            ->with([
                'user.creatorProfile',
                'media',
                'comments.user',
            ])
            ->latest('published_at')
            ->paginate(15);

        return view('feed.index', compact('posts'));
    }
}
