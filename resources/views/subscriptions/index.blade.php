@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 mb-1">My Subscriptions</h1>
    <p class="text-secondary mb-0">View and manage your creator subscriptions.</p>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    @forelse($subscriptions as $subscription)
        <div class="border rounded-4 p-3 mb-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $subscription->creator->creatorProfile->avatar_url }}"
                         width="56" height="56" class="rounded-circle" style="object-fit: cover;" alt="">
                    <div>
                        <div class="fw-bold">{{ $subscription->creator->creatorProfile->display_name }}</div>
                        <div class="small text-secondary">
                            ${{ number_format($subscription->amount, 2) }} · {{ ucfirst($subscription->status) }}
                        </div>
                    </div>
                </div>

                <div class="d-grid d-sm-flex gap-2">
                    <a href="{{ route('creators.show', $subscription->creator->creatorProfile->slug) }}" class="btn btn-primary btn-sm">
                        View Creator
                    </a>

                    @if($subscription->status === 'active')
                        <form method="POST" action="{{ route('subscriptions.cancel', $subscription->creator->username) }}">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary mb-0">You have no subscriptions yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
