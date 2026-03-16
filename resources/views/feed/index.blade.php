@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 mb-1">Your Feed</h1>
    <p class="text-secondary mb-0">Latest posts from creators you subscribe to.</p>
</div>

<div class="row g-4">
    @forelse($posts as $post)
        <div class="col-12">
            <div class="post-card">
                <div class="p-3 d-flex align-items-center gap-3 border-bottom">
                    <img src="{{ $post->user->creatorProfile->avatar_url }}"
                         width="56" height="56" class="rounded-circle" style="object-fit: cover;" alt="">
                    <div>
                        <div class="fw-bold">{{ $post->user->creatorProfile->display_name }}</div>
                        <div class="small text-secondary">{{ $post->published_at?->diffForHumans() }}</div>
                    </div>
                </div>

                @if($post->media->count())
                    @php $firstMedia = $post->media->first(); @endphp
                    @if($firstMedia->media_type === 'video')
                        <video class="w-100" controls style="max-height: 520px; background: #000;">
                            <source src="{{ $firstMedia->url }}" type="{{ $firstMedia->mime_type }}">
                        </video>
                    @else
                        <img src="{{ $firstMedia->url }}" class="w-100" style="max-height: 520px; object-fit: cover;" alt="">
                    @endif
                @endif

                <div class="p-3">
                    <p class="mb-3">{{ $post->caption }}</p>

                    <form method="POST" action="{{ route('comments.store', $post) }}" class="mb-3">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="body" class="form-control" placeholder="Add a comment...">
                            <button class="btn btn-outline-light">Comment</button>
                        </div>
                    </form>

                    @if($post->comments->count())
                        <div class="border-top pt-3">
                            @foreach($post->comments->take(3) as $comment)
                                <div class="mb-2">
                                    <span class="fw-semibold">{{ $comment->user->name }}:</span>
                                    <span class="text-light-emphasis">{{ $comment->body }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary">No feed posts yet. Subscribe to creators to populate your feed.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $posts->links() }}
</div>
@endsection
