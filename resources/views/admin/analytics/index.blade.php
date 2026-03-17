@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Analytics</h1>
        <p class="text-secondary mb-0">Platform growth, creator performance, and engagement signals.</p>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Admin</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Users</div><div class="h3 mb-0">{{ $stats['users_total'] }}</div></div></div>
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Creators</div><div class="h3 mb-0">{{ $stats['creators_total'] }}</div></div></div>
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Fans</div><div class="h3 mb-0">{{ $stats['fans_total'] }}</div></div></div>
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Posts</div><div class="h3 mb-0">{{ $stats['posts_total'] }}</div></div></div>
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Messages</div><div class="h3 mb-0">{{ $stats['messages_total'] }}</div></div></div>
    <div class="col-6 col-md-4 col-xl-2"><div class="bg-panel rounded-4 p-3"><div class="small text-secondary">Subs</div><div class="h3 mb-0">{{ $stats['active_subscriptions_total'] }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Top Creators by Tips</h2>

            @forelse($topCreatorsByTips as $creator)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div class="fw-bold">{{ $creator->creatorProfile?->display_name ?? $creator->name }}</div>
                        <div>${{ number_format($creator->tips_sum, 2) }}</div>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No tip data yet.</div>
            @endforelse
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Top Creators by Subscribers</h2>

            @forelse($topCreatorsBySubscribers as $creator)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div class="fw-bold">{{ $creator->creatorProfile?->display_name ?? $creator->name }}</div>
                        <div>{{ $creator->subscribers_count_metric }}</div>
                    </div>
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No subscriber data yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
