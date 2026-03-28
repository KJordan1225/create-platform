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
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Creator Dashboard</h1>
        <p class="text-secondary mb-0">Manage your profile, posts, subscribers, and earnings.</p>
    </div>

    <div class="d-grid d-sm-flex gap-2">
        <a href="{{ route('creator.posts.create') }}" class="btn btn-primary">New Post</a>
        <a href="{{ route('creator.posts.index') }}" class="btn btn-primary">Manage Posts</a>
        <a href="{{ route('creator.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
        <a href="{{ route('creator.earnings.index') }}" class="btn btn-primary">Earnings</a>
        <a href="{{ route('messages.index') }}" class="btn btn-primary">Inbox</a>
    </div>
</div>

<div class="mb-3">
    @include('creator.partials.stripe-status-badge', ['creator' => auth()->user()])
</div>

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="fw-semibold">Payout Status</div>
                <div class="text-muted small">Stripe onboarding and account readiness</div>
            </div>
            @include('creator.partials.stripe-status-badge', ['creator' => auth()->user()])
        </div>

        @if(!auth()->user()->isStripeConnected())
            <div class="mt-3 d-grid d-md-flex">
                <a href="{{ route('creator.settings.payouts') }}" class="btn btn-outline-primary rounded-3">
                    Manage Payout Setup
                </a>
            </div>
        @endif

        @if(!auth()->user()->hasActiveCreatorPlatformSubscription())
            <div class="alert alert-warning rounded-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <strong>Posting is locked.</strong><br>
                    Activate your creator subscription to publish new posts.
                </div>
                <div>
                    <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-primary rounded-pill px-4">
                        Activate Now
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="text-secondary small mb-1">Posts</div>
            <div class="display-6 fw-bold">{{ $postCount }}</div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="text-secondary small mb-1">Active Subscribers</div>
            <div class="display-6 fw-bold">{{ $subscriberCount }}</div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="text-secondary small mb-1">Tips Received</div>
            <div class="display-6 fw-bold">${{ number_format($tipTotal, 2) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Recent Subscribers</h2>

            @forelse($recentSubscribers as $subscription)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-bold">{{ $subscription->fan->name }}</div>
                            <div class="text-secondary small">{{ $subscription->fan->email }}</div>
                        </div>
                        <div class="text-end">
                            <div class="badge text-bg-success">{{ ucfirst($subscription->status) }}</div>
                            <div class="small text-secondary mt-1">{{ $subscription->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No subscribers yet.</div>
            @endforelse
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Recent Tips</h2>

            @forelse($recentTips as $tip)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-bold">{{ $tip->fan->name }}</div>
                            <div class="text-secondary small">{{ $tip->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="fw-bold">${{ number_format($tip->amount, 2) }}</div>
                    </div>

                    @if($tip->message)
                        <div class="small text-light-emphasis mt-2">{{ $tip->message }}</div>
                    @endif
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No tips yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
