<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostMediaController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->user_id === auth()->id(), 403);
        abort_unless(auth()->user()->canCreateCreatorPosts(), 403);

        $data = $request->validate([
            'media' => ['required', 'array'],
            'media.*' => ['file', 'max:20480'],
        ]);

        foreach ($data['media'] as $file) {
            $path = $file->store('posts', 'public');

            $post->media()->create([
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Media uploaded successfully.');
    }

    public function destroy(Post $post, $mediaId): RedirectResponse
    {
        abort_unless($post->user_id === auth()->id(), 403);
        abort_unless(auth()->user()->canCreateCreatorPosts(), 403);

        $media = $post->media()->findOrFail($mediaId);
        $media->delete();

        return back()->with('success', 'Media removed successfully.');
    }
}
