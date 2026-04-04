@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">My Posts</h1>
        <p class="text-secondary mb-0">Create and manage your locked and public posts.</p>
    </div>

    <a href="{{ route('creator.posts.create') }}" class="btn btn-primary">Create New Post</a>
</div>

<div class="row g-4">
    @forelse($posts as $post)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="post-card h-100">
                @php $firstMedia = $post->media->first(); @endphp

                @if($firstMedia)
                    @if($firstMedia->media_type === 'video')
                        @php 
                            $url = $firstMedia->url;
                            $path = str_replace('http://127.0.0.1:8000/storage/', '', $url);
                        @endphp
                        <a href="{{ route('videos.stream', $media) }}" class="text-decoration-none">
                        <video class="media-thumb" controls @if(!$post->canBeViewedBy(auth()->user())) poster="" @endif>
                            <source src="{{ asset('images/'.$firstMedia->file_path)}} type="{{ $firstMedia->mime_type }}">
                        </video>
                        </a>
                    @else
                        @php 
                            $url = $firstMedia->url;
                            $path = str_replace('http://127.0.0.1:8000/storage/', '', $url);
                        @endphp
                        <img src="{{ asset('images/'.$path) }}" class="media-thumb" alt="">
                    @endif
                @endif

                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge {{ $post->is_locked ? 'text-bg-dark' : 'text-bg-secondary' }}">
                            {{ $post->is_locked ? 'Locked' : 'Public' }}
                        </span>

                        <span class="badge {{ $post->is_published ? 'text-bg-success' : 'text-bg-warning' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>

                    <p class="mb-3">{{ \Illuminate\Support\Str::limit($post->caption, 120) }}</p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('creator.posts.edit', $post) }}" class="btn btn-outline-light">Edit</a>

                        <form method="POST" action="{{ route('creator.posts.destroy', $post) }}"
                              onsubmit="return confirm('Delete this post?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary">You have not created any posts yet.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $posts->links() }}
</div>
@endsection
