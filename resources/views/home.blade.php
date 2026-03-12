@extends('layouts.app')

@section('content')
<div class="row align-items-center g-4 mb-5">
    <div class="col-12 col-lg-6">
        <h1 class="display-5 fw-bold">Support creators. Unlock exclusive content.</h1>
        <p class="lead text-light-emphasis">
            Discover creators, subscribe to premium content, and send direct support through tips.
        </p>

        <div class="d-grid d-sm-flex gap-2">
            <a href="{{ route('explore.index') }}" class="btn btn-primary btn-lg">Explore Creators</a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Create Account</a>
            @endguest
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="p-4 rounded-4 bg-panel">
            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 rounded-4 bg-dark">
                        <div class="small text-secondary">Mobile Friendly</div>
                        <div class="fw-bold">Responsive everywhere</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4 bg-dark">
                        <div class="small text-secondary">Subscriptions</div>
                        <div class="fw-bold">Recurring support</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4 bg-dark">
                        <div class="small text-secondary">Tips</div>
                        <div class="fw-bold">Instant payments</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded-4 bg-dark">
                        <div class="small text-secondary">Locked Posts</div>
                        <div class="fw-bold">Subscriber access</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h3 mb-0">Featured Creators</h2>
        <a href="{{ route('explore.index') }}" class="btn btn-outline-light btn-sm">View all</a>
    </div>

    <div class="row g-4">
        @forelse($featuredCreators as $creator)
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="creator-card h-100">
                    <img src="{{ $creator->creatorProfile->banner_url }}" class="w-100" style="height: 140px; object-fit: cover;" alt="">
                    <div class="p-3">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $creator->creatorProfile->avatar_url }}" class="rounded-circle" width="56" height="56" style="object-fit: cover;" alt="">
                            <div>
                                <div class="fw-bold">{{ $creator->creatorProfile->display_name }}</div>
                                <div class="text-secondary small">${{ number_format($creator->creatorProfile->monthly_price, 2) }}/month</div>
                            </div>
                        </div>

                        <a href="{{ route('creators.show', $creator->creatorProfile->slug) }}" class="btn btn-primary w-100">
                            View Creator
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary">No creators found yet.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection
