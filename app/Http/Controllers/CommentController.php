<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\AbuseDetectionService;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post, AbuseDetectionService $abuseDetectionService)
    {
        if (! $post->is_published) {
            abort(404);
        }

        if ($abuseDetectionService->isCommentSpam($request->user(), $request->validated('body'))) {
            return back()->withErrors([
                'body' => 'Your comment was flagged as suspicious. Please revise and try again.',
            ]);
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
