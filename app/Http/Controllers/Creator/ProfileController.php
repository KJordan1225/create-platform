<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = auth()->user()
            ->posts()
            ->with('media')
            ->latest()
            ->paginate(12);

        return view('creator.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('creator.posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $post = auth()->user()->posts()->create([
            'caption' => $data['caption'] ?? null,
            'is_locked' => $request->boolean('is_locked'),
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true) ? now() : null,
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $index => $file) {
                $path = $file->store('posts', 'public');

                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'media_type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);

        $post->load('media');

        return view('creator.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);

        $data = $request->validated();

        $post->update([
            'caption' => $data['caption'] ?? null,
            'is_locked' => $request->boolean('is_locked'),
            'is_published' => $request->boolean('is_published', true),
            'published_at' => $request->boolean('is_published', true)
                ? ($post->published_at ?? now())
                : null,
        ]);

        if ($request->hasFile('media')) {
            $currentCount = $post->media()->count();

            foreach ($request->file('media') as $index => $file) {
                $path = $file->store('posts', 'public');

                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'media_type' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image',
                    'sort_order' => $currentCount + $index,
                ]);
            }
        }

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);

        $post->load('media');

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $post->delete();

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Post deleted.');
    }
}
