@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">{{ $creator->creatorProfile?->display_name ?? $creator->name }}</h1>
        <p class="text-secondary mb-0">{{ '@' . $creator->username }} · {{ $creator->email }}</p>
    </div>

    <div class="d-grid d-sm-flex gap-2">
        @if(!$creator->creator_approved_at)
            <form method="POST" action="{{ route('admin.creators.approve', $creator) }}">
                @csrf
                <button class="btn btn-success">Approve Creator</button>
            </form>
        @endif

        @if($creator->is_active)
            <form method="POST" action="{{ route('admin.creators.suspend', $creator) }}">
                @csrf
                <button class="btn btn-outline-danger">Suspend</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.creators.reactivate', $creator) }}">
                @csrf
                <button class="btn btn-primary">Reactivate</button>
            </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-5">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Profile</h2>

            @if($creator->creatorProfile)
                <img src="{{ $creator->creatorProfile->banner_url }}" class="img-fluid rounded mb-3 w-100" style="max-height: 220px; object-fit: cover;" alt="">
                <div class="mb-2"><strong>Display Name:</strong> {{ $creator->creatorProfile->display_name }}</div>
                <div class="mb-2"><strong>Slug:</strong> {{ $creator->creatorProfile->slug }}</div>
                <div class="mb-2"><strong>Price:</strong> ${{ number_format($creator->creatorProfile->monthly_price, 2) }}/month</div>
                <div class="mb-2"><strong>Tips:</strong> {{ $creator->creatorProfile->allow_tips ? 'Enabled' : 'Disabled' }}</div>
                <div class="mb-0"><strong>Bio:</strong><br>{{ $creator->creatorProfile->bio }}</div>
            @else
                <div class="alert alert-secondary mb-0">No creator profile found.</div>
            @endif
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="bg-panel rounded-4 p-3 p-md-4">
            <h2 class="h5 mb-3">Recent Posts</h2>

            <div class="row g-3">
                @forelse($creator->posts as $post)
                    <div class="col-12 col-md-6">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $post->is_locked ? 'text-bg-dark' : 'text-bg-secondary' }}">
                                    {{ $post->is_locked ? 'Locked' : 'Public' }}
                                </span>
                                <span class="badge {{ $post->is_published ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ $post->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>

                            <div class="mb-2">{{ \Illuminate\Support\Str::limit($post->caption, 120) }}</div>
                            <div class="small text-secondary">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-secondary mb-0">No posts yet.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
