@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7 col-xl-6">
        <div class="bg-panel rounded-4 p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="{{ $profile->avatar_url }}" class="rounded-circle mb-3" width="88" height="88" style="object-fit: cover;" alt="">
                <h1 class="h2 mb-1">Send a Tip to {{ $profile->display_name }}</h1>
                <p class="text-secondary mb-0">Support this creator with a one-time payment.</p>
            </div>

            <form method="POST" action="{{ route('tips.store', $creator->username) }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tip Amount</label>
                    <input type="number" name="amount" class="form-control" min="1" max="500" step="0.01"
                           value="{{ old('amount', '10.00') }}">
                </div>

                <div class="mb-4">
                    <label class="form-label">Message (optional)</label>
                    <textarea name="message" rows="4" class="form-control">{{ old('message') }}</textarea>
                </div>

                <button class="btn btn-primary btn-lg w-100">Continue to Secure Checkout</button>
            </form>

            <div class="small text-secondary mt-3 text-center">
                Tips are processed securely by Stripe.
            </div>
        </div>
    </div>
</div>
@endsection