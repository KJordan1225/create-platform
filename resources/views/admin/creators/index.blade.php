@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 mb-1">Manage Creators</h1>
    <p class="text-secondary mb-0">Review, approve, suspend, and reactivate creator accounts.</p>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    <div class="row g-3">
        @forelse($creators as $creator)
            <div class="col-12">
                <div class="border rounded-4 p-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $creator->creatorProfile?->avatar_url ?? asset('images/default-avatar.png') }}"
                                 width="56" height="56" class="rounded-circle" style="object-fit: cover;" alt="">
                            <div>
                                <div class="fw-bold">{{ $creator->creatorProfile?->display_name ?? $creator->name }}</div>
                                <div class="small text-secondary">{{ '@' . $creator->username }} · {{ $creator->email }}</div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                            @if($creator->creator_approved_at)
                                <span class="badge text-bg-success">Approved</span>
                            @else
                                <span class="badge text-bg-warning">Pending</span>
                            @endif

                            @if($creator->is_active)
                                <span class="badge text-bg-primary">Active</span>
                            @else
                                <span class="badge text-bg-danger">Suspended</span>
                            @endif

                            <a href="{{ route('admin.creators.show', $creator) }}" class="btn btn-outline-light btn-sm">View</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary mb-0">No creators found.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $creators->links() }}
    </div>
</div>
@endsection
