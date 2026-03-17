<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class ModerationController extends Controller
{
    public function hidePost(Post $post): RedirectResponse
    {
        $post->update([
            'is_published' => false,
        ]);

        return back()->with('success', 'Post hidden successfully.');
    }

    public function publishPost(Post $post): RedirectResponse
    {
        $post->update([
            'is_published' => true,
            'published_at' => $post->published_at ?? now(),
        ]);

        return back()->with('success', 'Post published successfully.');
    }

    public function hideComment(Comment $comment): RedirectResponse
    {
        $comment->update([
            'is_visible' => false,
        ]);

        return back()->with('success', 'Comment hidden successfully.');
    }

    public function showComment(Comment $comment): RedirectResponse
    {
        $comment->update([
            'is_visible' => true,
        ]);

        return back()->with('success', 'Comment restored successfully.');
    }

    public function deletePost(Post $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'Post removed successfully.');
    }

    public function deleteComment(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Comment removed successfully.');
    }
}
