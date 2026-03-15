@extends('layouts.app')

@section('content')
<div class="creator-card mb-4">
    <img src="{{ $profile->banner_url }}" class="banner-cover" alt="Banner">

    <div class="p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
            <div>
                <img src="{{ $profile->avatar_url }}" class="avatar-lg" alt="Avatar">
            </div>

            <div class="flex-grow-1">
                <h1 class="h3 mb-1">{{ $profile->display_name }}</h1>
                <div class="text-secondary mb-2">{{ '@' . $creator->username }}</div>
                <p class="mb-0 text-light-emphasis">{{ $profile->bio }}</p>
            </div>

            <div class="d-grid gap-2 w-100 w-md-auto">
                <div class="fw-bold text-center text-md-end">${{ number_format($profile->monthly_price, 2) }}/month</div>

                @auth
                    @if(auth()->id() !== $creator->id)
                        @if($isSubscribed)
                            <form method="POST" action="{{ route('subscriptions.cancel', $creator->username) }}">
                                @csrf
                                <button class="btn btn-outline-danger w-100">Cancel Subscription</button>
                            </form>
                        @else
                            <a href="{{ route('subscriptions.checkout', $creator->username) }}" class="btn btn-primary w-100">
                                Subscribe
                            </a>
                        @endif

                        @if($profile->allow_tips)
                            <a href="{{ route('tips.checkout', $creator->username) }}" class="btn btn-outline-light w-100">
                                Send Tip
                            </a>
                        @endif
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Subscribe</a>
                @endauth

                @auth
                    @if(auth()->id() !== $creator->id)
                        <form method="POST" action="{{ route('reports.store') }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="reportable_type" value="creator">
                            <input type="hidden" name="reportable_id" value="{{ $profile->id }}">
                            <input type="hidden" name="reason" value="Creator profile review requested">
                            <button class="btn btn-sm btn-outline-warning">Report Creator</button>
                        </form>
                    @endif
                @endauth

            </div>
        </div>
    </div>
</div>

<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Posts</h2>
        <span class="text-secondary small">{{ $posts->count() }} posts</span>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-12 col-lg-6">
                <div class="post-card h-100">
                    @php $firstMedia = $post->media->first(); @endphp

                    @if($firstMedia)
                        <div class="position-relative">
                            @if($firstMedia->media_type === 'video')
                                <video class="media-thumb" controls @if(!$post->canBeViewedBy(auth()->user())) poster="" @endif>
                                    <source src="{{ $firstMedia->url }}" type="{{ $firstMedia->mime_type }}">
                                </video>
                            @else
                                <img src="{{ $firstMedia->url }}" class="media-thumb" alt="">
                            @endif

                            @if(!$post->canBeViewedBy(auth()->user()))
                                <div class="locked-overlay">
                                    <div>
                                        <div class="fw-bold mb-1">Locked Content</div>
                                        <div class="small">Subscribe to unlock this post.</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="p-3">
                        <div class="small text-secondary mb-2">
                            {{ $post->published_at?->diffForHumans() ?? $post->created_at->diffForHumans() }}
                        </div>

                        <p class="mb-0">
                            @if($post->canBeViewedBy(auth()->user()))
                                {{ $post->caption }}
                            @else
                                {{ \Illuminate\Support\Str::limit($post->caption, 120) }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary">This creator has not published any posts yet.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection
