@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Explore Creators</h1>
        <p class="text-secondary mb-0">Find creators and unlock premium content.</p>
    </div>

    <form method="GET" action="{{ route('explore.index') }}" class="w-100">
        <div class="row g-2">
            <div class="col-12 col-lg-5">
                <input type="text" name="search" class="form-control" placeholder="Search creators..." value="{{ $search }}">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <input type="number" name="max_price" step="0.01" min="1" class="form-control" placeholder="Max monthly price" value="{{ $maxPrice }}">
            </div>
            <div class="col-12 col-md-6 col-lg-2 d-flex align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="tips_only" value="1" id="tips_only" {{ $tipsOnly ? 'checked' : '' }}>
                    <label class="form-check-label" for="tips_only">Tips only</label>
                </div>
            </div>
            <div class="col-12 col-lg-2">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

</div>

<div class="row g-4">
    @forelse($creators as $creator)
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="creator-card h-100">
                <img src="{{ $creator->creatorProfile->banner_url }}" class="w-100" style="height: 180px; object-fit: cover;" alt="">
                <div class="p-3">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $creator->creatorProfile->avatar_url }}" width="64" height="64" class="rounded-circle" style="object-fit: cover;" alt="">
                        <div>
                            <div class="fw-bold">{{ $creator->creatorProfile->display_name }}</div>
                            <div class="text-secondary small">{{ '@' . $creator->username }}</div>
                        </div>
                    </div>

                    <p class="text-secondary small">
                        {{ \Illuminate\Support\Str::limit($creator->creatorProfile->bio, 110) }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge text-bg-dark">${{ number_format($creator->creatorProfile->monthly_price, 2) }}/month</span>
                        <a href="{{ route('creators.show', $creator->creatorProfile->slug) }}" class="btn btn-primary btn-sm">
                            View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary">No creators matched your search.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $creators->links() }}
</div>
@endsection
