@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7 col-xl-6">
        <div class="bg-panel rounded-4 p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="{{ $profile->avatar_url }}" class="rounded-circle mb-3" width="88" height="88" style="object-fit: cover;" alt="">
                <h1 class="h2 mb-1">Subscribe to {{ $profile->display_name }}</h1>
                <p class="text-secondary mb-0">Unlock premium posts and support this creator.</p>
            </div>

            <div class="border rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold">Monthly Subscription</div>
                        <div class="text-secondary small">Recurring access</div>
                    </div>
                    <div class="h4 mb-0">${{ number_format($profile->monthly_price, 2) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('subscriptions.store', $creator->username) }}">
                @csrf
                <button class="btn btn-primary btn-lg w-100">Continue to Secure Checkout</button>
            </form>

            <div class="small text-secondary mt-3 text-center">
                Payments are processed securely by Stripe.
            </div>
        </div>
    </div>
</div>
@endsection