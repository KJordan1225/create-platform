@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-center">
                    <h1 class="h3 mb-3">Tip Sent</h1>
                    <p class="mb-2">
                        Your ${{ number_format((float) $tip->amount, 2) }} tip was sent to
                        <strong>{{ $creator->creatorProfile->display_name ?? $creator->name }}</strong>.
                    </p>
                    <p class="text-muted mb-4">
                        Status: {{ ucfirst($tip->status) }}
                    </p>

                    <div class="d-grid">
                        <a href="{{ route('creators.show', $creator->creatorProfile->slug) }}"
                           class="btn btn-primary btn-lg rounded-3">
                            Back to Creator
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
