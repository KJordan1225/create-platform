@extends('layouts.app')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="mb-4">
                <h1 class="h3 fw-bold mb-2">Creator Subscription</h1>
                <p class="text-secondary mb-0">
                    Manage posting access, billing, and creator plan status.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4 shadow-sm border-0">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger rounded-4 shadow-sm border-0">
                    {{ session('error') }}
                </div>
            @endif

            @if ($subscription)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-12 col-lg-7">
                                <div class="text-uppercase small fw-semibold mb-1 {{ $subscription->isActive() ? 'text-success' : 'text-danger' }}">
                                    Current Subscription
                                </div>

                                <h2 class="h4 mb-2">
                                    {{ $subscription->plan->name ?? 'Creator Plan' }}
                                </h2>

                                <div class="mb-2">
                                    <span class="badge text-bg-{{ $subscription->statusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                                    </span>
                                </div>

                                <p class="text-secondary mb-1">
                                    @if ($subscription->willCancelAtPeriodEnd() && $subscription->ends_at)
                                        Your subscription is scheduled to end on {{ $subscription->ends_at->format('M d, Y') }}.
                                    @elseif ($subscription->renews_at)
                                        Your subscription renews on {{ $subscription->renews_at->format('M d, Y') }}.
                                    @else
                                        Your subscription status is being tracked locally.
                                    @endif
                                </p>

                                @if ($subscription->isActive())
                                    <p class="mb-0 text-success">
                                        Posting access is currently enabled.
                                    </p>
                                @else
                                    <p class="mb-0 text-danger">
                                        Posting access is currently disabled.
                                    </p>
                                @endif

                                @if ($subscription?->is_trial && $subscription?->trial_ends_at)
                                    <div class="alert alert-info rounded-4 shadow-sm border-0 mt-3 mb-0">
                                        You are currently on a trial plan until {{ $subscription->trial_ends_at->format('M d, Y') }}.
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-5">
                                <div class="d-grid gap-2">
                                    @if ($subscription->stripe_customer_id)
                                        <form method="POST" action="{{ route('creator.billing.portal') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary rounded-pill w-100">
                                                Open Billing Portal
                                            </button>
                                        </form>
                                    @endif

                                    @if ($subscription->isActive() && !$subscription->willCancelAtPeriodEnd())
                                        <form method="POST" action="{{ route('creator.billing.cancel-at-period-end') }}"
                                              onsubmit="return confirm('Schedule cancellation at the end of your current billing period?')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning rounded-pill w-100">
                                                Cancel at Period End
                                            </button>
                                        </form>
                                    @endif

                                    @if ($subscription->isActive() && $subscription->willCancelAtPeriodEnd())
                                        <form method="POST" action="{{ route('creator.billing.reactivate') }}"
                                              onsubmit="return confirm('Reactivate your creator subscription?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success rounded-pill w-100">
                                                Reactivate Subscription
                                            </button>
                                        </form>
                                    @endif

                                    @if ($subscription->isActive())
                                        <form method="POST" action="{{ route('creator.billing.cancel-now') }}"
                                              onsubmit="return confirm('Cancel immediately? Posting access may stop right away.')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                                                Cancel Immediately
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (!$subscription || !$subscription->isActive())
                <div class="row g-3">
                    @forelse ($plans as $plan)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-center">
                                        <div class="col-12 col-md-7">
                                            <h2 class="h4 mb-2">{{ $plan->name }}</h2>
                                            <p class="text-secondary mb-3">{{ $plan->description }}</p>

                                            @if (!empty($plan->features))
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($plan->features as $feature)
                                                        <li class="mb-2 d-flex align-items-start">
                                                            <span class="me-2 text-success">✓</span>
                                                            <span>{{ $feature }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>

                                        <div class="col-12 col-md-5">
                                            <div class="bg-light rounded-4 p-4 text-center">
                                                <div class="display-6 fw-bold mb-1">
                                                    ${{ number_format($plan->price / 100, 2) }}
                                                </div>
                                                <div class="text-secondary mb-3">per {{ $plan->interval }}</div>

                                                <form method="POST" action="{{ route('creator.billing.checkout', $plan) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100">
                                                        Subscribe Now
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning rounded-4 shadow-sm border-0 mb-0">
                                No creator plans are available right now.
                            </div>
                        </div>
                    @endforelse
                </div>
            @endif

            @include('creator.billing.partials.subscription-history', ['history' => $history])

            <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('creator.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Back to Dashboard
                </a>

                
            </div>
        </div>
    </div>
</div>
@endsection
