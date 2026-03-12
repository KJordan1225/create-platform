<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:10000'],
            'is_locked' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'media.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:20480'],
        ]);

        $post = auth()->user()->posts()->create([
            'caption' => $data['caption'] ?? null,
            'is_locked' => $request->boolean('is_locked'),
            'is_published' => $request->boolean('is_published', true),
            'published_at' => now(),
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

    public function update(Request $request, Post $post)
    {
        abort_unless($post->user_id === auth()->id(), 403);

        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:10000'],
            'is_locked' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'media.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:20480'],
        ]);

        $post->update([
            'caption' => $data['caption'] ?? null,
            'is_locked' => $request->boolean('is_locked'),
            'is_published' => $request->boolean('is_published', true),
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

        foreach ($post->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $post->delete();

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Post deleted.');
    }
}
