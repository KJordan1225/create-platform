<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        if (! $post->is_published) {
            abort(404);
        }

        Comment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
            'is_visible' => true,
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }
}