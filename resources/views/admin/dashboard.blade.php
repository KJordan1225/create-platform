@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Admin Dashboard</h1>
        <p class="text-secondary mb-0">Manage creators, subscriptions, and platform health.</p>
    </div>

    <div class="d-grid d-sm-flex gap-2">
        <a href="{{ route('admin.creators.index') }}" class="btn btn-primary">Manage Creators</a>
        <a href="{{ route('admin.creator-subscriptions.index') }}">Creator Subscription Admin</a>
        <a href="{{ route('admin.webhook-logs.index') }}">Webhook Logs</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">Review Reports</a>
        <a href="{{ route('admin.analytics.index') }}" class="btn btn-primary">Analytics</a>
    </div>
</div>



<div class="row g-4 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Users</div>
            <div class="h3 mb-0">{{ $stats['users_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Creators</div>
            <div class="h3 mb-0">{{ $stats['creators_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Approved</div>
            <div class="h3 mb-0">{{ $stats['approved_creators_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Pending</div>
            <div class="h3 mb-0">{{ $stats['pending_creators_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Subscriptions</div>
            <div class="h3 mb-0">{{ $stats['active_subscriptions_count'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="bg-panel rounded-4 p-3 h-100">
            <div class="small text-secondary">Tips</div>
            <div class="h3 mb-0">${{ number_format($stats['tips_total'], 2) }}</div>
        </div>
    </div>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    <h2 class="h5 mb-3">Pending Creator Applications</h2>

    @forelse($pendingCreators as $creator)
        <div class="border rounded-4 p-3 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
                <div>
                    <div class="fw-bold">{{ $creator->creatorProfile?->display_name ?? $creator->name }}</div>
                    <div class="text-secondary small">{{ $creator->email }}</div>
                </div>

                <div class="d-grid d-sm-flex gap-2">
                    <a href="{{ route('admin.creators.show', $creator) }}" class="btn btn-primary btn-sm">Review</a>
                    <form method="POST" action="{{ route('admin.creators.approve', $creator) }}">
                        @csrf
                        <button class="btn btn-success btn-sm">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary mb-0">No pending creator applications.</div>
    @endforelse
</div>
@endsection
