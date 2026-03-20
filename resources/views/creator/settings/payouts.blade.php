@extends('layouts.app')

@section('content')
<div class="container py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">

            @if(session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning rounded-4">{{ session('warning') }}</div>
            @endif

            @if(session('info'))
                <div class="alert alert-info rounded-4">{{ session('info') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1">Payout Settings</h1>
                    <p class="text-muted mb-0">Connect Stripe to receive subscription and tip payouts.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 p-md-4">

                    <div class="mb-3">
                        @include('creator.partials.stripe-status-badge', ['creator' => $creator])
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="border rounded-4 p-3 h-100">
                                <div class="small text-muted mb-1">Stripe Account ID</div>
                                <div class="fw-semibold">
                                    {{ $creator->stripe_account_id ?: 'Not connected yet' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100">
                                <div class="small text-muted mb-1">Charges</div>
                                <div class="fw-semibold">
                                    {{ $creator->stripe_charges_enabled ? 'Enabled' : 'Disabled' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <div class="border rounded-4 p-3 h-100">
                                <div class="small text-muted mb-1">Payouts</div>
                                <div class="fw-semibold">
                                    {{ $creator->stripe_payouts_enabled ? 'Enabled' : 'Disabled' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $requirements = $creator->stripe_requirements ?? [];
                        $currentlyDue = $requirements['currently_due'] ?? [];
                        $pastDue = $requirements['past_due'] ?? [];
                        $pendingVerification = $requirements['pending_verification'] ?? [];
                        $disabledReason = $requirements['disabled_reason'] ?? null;
                    @endphp

                    @if($creator->stripe_onboarding_status === 'connected')
                        <div class="alert alert-success rounded-4">
                            Your payout account is connected and ready to receive funds.
                        </div>
                    @elseif($creator->stripe_onboarding_status === 'needs_action')
                        <div class="alert alert-warning rounded-4">
                            Stripe needs more information before your account can fully receive payouts.
                        </div>

                        @if($disabledReason)
                            <div class="small text-muted mb-3">
                                <strong>Reason:</strong> {{ $disabledReason }}
                            </div>
                        @endif

                        @if(!empty($currentlyDue))
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">Currently Due</div>
                                <ul class="mb-0">
                                    @foreach($currentlyDue as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($pastDue))
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">Past Due</div>
                                <ul class="mb-0">
                                    @foreach($pastDue as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($pendingVerification))
                            <div class="mb-3">
                                <div class="fw-semibold mb-2">Pending Verification</div>
                                <ul class="mb-0">
                                    @foreach($pendingVerification as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info rounded-4">
                            Start Stripe onboarding so you can receive creator payouts.
                        </div>
                    @endif

                    <div class="d-grid d-md-flex gap-2">
                        <a href="{{ route('creator.stripe.connect') }}" class="btn btn-primary btn-lg rounded-3">
                            {{ $creator->stripe_account_id ? 'Continue Stripe Onboarding' : 'Connect Stripe' }}
                        </a>

                        @if($creator->stripe_account_id)
                            <a href="{{ route('creator.stripe.connect') }}" class="btn btn-outline-primary btn-lg rounded-3">
                                Refresh Stripe Status
                            </a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
