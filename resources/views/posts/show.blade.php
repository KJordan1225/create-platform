@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="post-card">
            <div class="p-3 d-flex align-items-center gap-3 border-bottom">
                <img src="{{ $post->user->creatorProfile->avatar_url }}"
                     width="60" height="60" class="rounded-circle" style="object-fit: cover;" alt="">
                <div>
                    <div class="fw-bold">{{ $post->user->creatorProfile->display_name }}</div>
                    <div class="small text-secondary">{{ $post->published_at?->format('M j, Y g:i A') }}</div>
                </div>
            </div>

            @if($post->media->count())
                @php $firstMedia = $post->media->first(); @endphp

                @if($canView)
                    @if($firstMedia->media_type === 'video')
                        <video class="w-100" controls style="max-height: 640px; background: #000;">
                            <source src="{{ $firstMedia->url }}" type="{{ $firstMedia->mime_type }}">
                        </video>
                    @else
                        <img src="{{ $firstMedia->url }}" class="w-100" style="max-height: 640px; object-fit: cover;" alt="">
                    @endif
                @else
                    <div class="position-relative">
                        @if($firstMedia->media_type === 'video')
                            <div class="bg-dark" style="height: 420px;"></div>
                        @else
                            <img src="{{ $firstMedia->url }}" class="w-100" style="max-height: 640px; object-fit: cover; filter: blur(18px);" alt="">
                        @endif

                        <div class="locked-overlay">
                            <div>
                                <div class="fw-bold mb-2">Locked Post</div>
                                <div class="small">Subscribe to unlock this content.</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="p-3 p-md-4">
                <p class="mb-0">
                    @if($canView)
                        {{ $post->caption }}
                    @else
                        {{ \Illuminate\Support\Str::limit($post->caption, 180) }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="bg-panel rounded-4 p-3 p-md-4">
            <h2 class="h5 mb-3">Comments</h2>

            @auth
                <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <textarea name="body" rows="4" class="form-control" placeholder="Write a comment..."></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Post Comment</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-4">Login to Comment</a>
            @endauth

            @forelse($post->comments as $comment)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="fw-semibold">{{ $comment->user->name }}</div>
                    <div class="small text-secondary mb-2">{{ $comment->created_at->diffForHumans() }}</div>
                    <div>{{ $comment->body }}</div>
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No comments yet.</div>
            @endforelse
        </div>

        <div class="mt-3 d-grid gap-2">
            <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">View Post</a>
        </div>
    </div>
</div>
@endsection
