@extends('layouts.app')

@section('content')
<style>
    .btn-primary {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-primary:focus {
        color: #fff;
        background-color: #0b5ed7;
        border-color: #0a58ca;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.5);
    }

    .btn-primary:active {
        color: #fff;
        background-color: #0a58ca;
        border-color: #0a53be;
    }

    .btn-primary:disabled {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
        opacity: 0.65;
    }

    .btn-blue-outline {
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background-color: transparent;
    }

    .btn-blue-outline:hover {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
    }
</style>
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
						<form method="POST" action="{{ route('reports.store') }}" class="mt-2">
							@csrf
							<input type="hidden" name="reportable_type" value="creator">
							<input type="hidden" name="reportable_id" value="{{ $profile->id }}">
							<input type="hidden" name="reason" value="Creator profile review requested">
							<button class="btn btn-sm btn-outline-warning">Report Creator</button>
						</form>
					@endif
				@endauth

                @auth
                    @if(auth()->id() !== $creator->id)
                        @if($isSubscribed)
                            <form method="POST" action="{{ route('subscriptions.cancel', $creator->username) }}">
                                @csrf
                                <button class="btn btn-outline-danger w-100">Cancel Subscription</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('subscriptions.checkout', $creator) }}">
								@csrf
								<button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
									Subscribe Now
								</button>
							</form>
                        @endif

                        @if($profile->allow_tips)
                            <form method="POST" action="{{ route('tips.store', $creator) }}">
								@csrf

								<div class="row g-3">
									<div class="col-12 col-md-4">
										<label class="form-label">Amount</label>
										<div class="input-group">
											<span class="input-group-text">$</span>
											<input
												type="number"
												name="amount"
												min="1"
												step="0.01"
												class="form-control"
												placeholder="10.00"
												required
											>
										</div>
									</div>

									<div class="col-12 col-md-8">
										<label class="form-label">Message</label>
										<input
											type="text"
											name="message"
											class="form-control"
											maxlength="500"
											placeholder="Optional encouragement..."
										>
									</div>

									<div class="col-12">
										<button type="submit" class="btn btn-outline-primary btn-lg w-100 rounded-3">
											Send Tip
										</button>
									</div>
								</div>
							</form>

                        @endif
						
						@if(auth()->id() !== $creator->id)
							<form method="POST" action="{{ route('messages.start', $creator->username) }}" class="d-grid gap-2 mt-2">
								@csrf
								<input type="hidden" name="body" value="Hi {{ $profile->display_name }}, I’d like to connect.">
								<button class="btn btn-outline-light w-100">Message Creator</button>
							</form>
						@endif
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Subscribe</a>
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
                                @php 
                                	$url = $firstMedia->url;
                                	$path = str_replace('http://127.0.0.1:8000/storage/', '', $url);
                                @endphp
                                <a href="{{ route('videos.stream', $firstMedia) }}" class="text-decoration-none">
                                <video class="media-thumb" controls @if(!$post->canBeViewedBy(auth()->user())) poster="" @endif>
                                    <source src="{{ asset('images/'.$path) }}" type="{{ $firstMedia->mime_type }}">
                                </video> 
                                </a>                               
                            @else
                                @php 
                                    $url = $firstMedia->url;
                                    $path = str_replace('http://127.0.0.1:8000/storage/', '', $url);
                                @endphp
                                <img src="{{ asset('images/'.$path) }}" class="media-thumb" alt="">
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
					<div class="mt-3 d-grid gap-2">
						<a href="{{ route('posts.show', $post) }}" class="btn btn-primary">View Post</a>
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
