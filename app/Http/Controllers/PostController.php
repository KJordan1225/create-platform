<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    
    public function show(Request $request, Post $post): View
    {
        abort_unless($post->is_published, 404);

        $post->load([
            'user.creatorProfile',
            'media',
            'comments.user',
        ]);

        $viewer = $request->user();

        $canView = true;

        if ($post->is_locked) {
            $canView = $viewer
                ? (
                    $viewer->id === $post->user_id
                    || $viewer->isAdmin()
                    || $viewer->hasActiveSubscriptionTo($post->user)
                )
                : false;
        }

        return view('posts.show', compact('post', 'canView'));
    }
}
