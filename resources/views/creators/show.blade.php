@extends('layouts.app')

@section('content')
<div class="container py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            @if ($errors->any())
                <div class="alert alert-danger rounded-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                        <div class="flex-grow-1">
                            <h1 class="h3 mb-1">{{ $creator->creatorProfile->display_name ?? $creator->name }}</h1>
                            <div class="text-muted mb-2">{{ $creator->username }}</div>

                            @if (!empty($creator->creatorProfile?->bio))
                                <p class="mb-0">{{ $creator->creatorProfile->bio }}</p>
                            @endif
                        </div>

                        <div class="text-md-end">
                            <div class="fw-semibold">
                                ${{ number_format((float) $creator->creatorProfile->monthly_price, 2) }}/month
                            </div>
                            <small class="text-muted">80% creator / 20% platform</small>
                        </div>
                    </div>

                    @auth
                        @if (auth()->id() !== $creator->id)
                            <div class="mt-4">
                                <div class="d-grid gap-2">
                                    <form method="POST" action="{{ route('subscriptions.checkout', $creator) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                                            Subscribe Now
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div>
                                <h2 class="h5 mb-3">Send a Tip</h2>

                                <form method="POST" action="{{ route('tips.store', $creator) }}">
                                    @csrf

                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <label class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input
                                                    type="number"
                                                    name="amount"
                                                    min="1"
                                                    step="0.01"
                                                    class="form-control"
                                                    placeholder="10.00"
                                                    required
                                                >
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-8">
                                            <label class="form-label">Message</label>
                                            <input
                                                type="text"
                                                name="message"
                                                class="form-control"
                                                maxlength="500"
                                                placeholder="Optional encouragement..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <button type="submit" class="btn btn-outline-primary btn-lg w-100 rounded-3">
                                                Send Tip
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="mt-4 d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-3">Log In to Subscribe</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg rounded-3">Log In to Tip</a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
