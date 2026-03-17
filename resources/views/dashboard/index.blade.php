@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
    <div>
        <h1 class="h2 mb-1">Fan Dashboard</h1>
        <p class="text-secondary mb-0">Manage your subscriptions and recent support activity.</p>
    </div>

    @if(auth()->user()->is_creator && is_null(auth()->user()->creator_approved_at))
        <div class="alert alert-warning">
            Your creator application is pending approval. You’ll gain creator dashboard access once approved.
        </div>
    @endif`
    
    <div class="d-grid d-sm-flex gap-2">
        @if(!auth()->user()->is_creator)
            <a href="{{ route('creator.apply') }}" class="btn btn-primary">Become a Creator</a>
        @endif
        <a href="{{ route('feed.index') }}" class="btn btn-outline-light">Open Feed</a>
        <a href="{{ route('messages.index') }}" class="btn btn-outline-light">Messages</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-7">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Your Subscriptions</h2>

            @forelse($subscriptions as $subscription)
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 border rounded-4 p-3 mb-3">
                    <div>
                        <div class="fw-bold">
                            {{ $subscription->creator->creatorProfile->display_name ?? $subscription->creator->name }}
                        </div>
                        <div class="text-secondary small">
                            Status: {{ ucfirst($subscription->status) }}
                        </div>
                    </div>

                    <div class="text-md-end">
                        <div>${{ number_format($subscription->amount, 2) }}</div>
                        <a href="{{ route('creators.show', $subscription->creator->creatorProfile->slug) }}" class="btn btn-sm btn-outline-light mt-2">
                            View Creator
                        </a>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary mb-0">You do not have any subscriptions yet.</div>
            @endforelse
        </div>
    </div>

    <div class="col-12 col-xl-5">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Recent Tips</h2>

            @forelse($tips as $tip)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-bold">
                                {{ $tip->creator->creatorProfile->display_name ?? $tip->creator->name }}
                            </div>
                            <div class="text-secondary small">{{ ucfirst($tip->status) }}</div>
                        </div>
                        <div class="fw-bold">${{ number_format($tip->amount, 2) }}</div>
                    </div>

                    @if($tip->message)
                        <div class="small text-light-emphasis mt-2">{{ $tip->message }}</div>
                    @endif
                </div>
            @empty
                <div class="alert alert-secondary mb-0">You have not sent any tips yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
