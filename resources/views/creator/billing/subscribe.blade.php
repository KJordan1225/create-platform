@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            <div class="mb-4">
                <h1 class="h3 mb-2">Creator Subscription</h1>
                <p class="text-muted mb-0">
                    Activate your creator plan to publish subscriber content.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
            @endif

            @if($subscription && $subscription->isActive())
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Current Status</h2>
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="badge text-bg-success px-3 py-2">Active</span>
                            <span class="text-muted small">
                                Renews:
                                {{ optional($subscription->renews_at)->format('M d, Y') ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                @forelse($plans as $plan)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div>
                                        <h2 class="h5 mb-1">{{ $plan->name }}</h2>
                                        <p class="text-muted mb-0">{{ $plan->description }}</p>
                                    </div>

                                    <div class="text-md-end">
                                        <div class="fs-4 fw-bold mb-2">
                                            ${{ number_format($plan->price / 100, 2) }}/{{ $plan->interval }}
                                        </div>

                                        <form method="POST" action="{{ route('creator.billing.checkout', $plan) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 w-md-auto">
                                                Subscribe to Create
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning rounded-4 mb-0">
                            No creator plans are available right now.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                <a href="{{ route('creator.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Back to Dashboard
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
