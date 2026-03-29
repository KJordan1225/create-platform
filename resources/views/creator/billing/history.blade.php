@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Billing History</h1>
            <p class="text-secondary mb-0">View your creator subscription activity and plan changes.</p>
        </div>

        <div>
            <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-outline-secondary rounded-pill px-4">
                Back to Subscription
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @forelse ($history as $item)
                <div class="py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-3">
                            <div class="fw-semibold">{{ $item->plan->name ?? 'Creator Plan' }}</div>
                            <div class="small text-secondary">
                                {{ ucfirst($item->provider) }}
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <span class="badge text-bg-{{ $item->statusBadgeClass() }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </div>

                        <div class="col-6 col-md-2">
                            @if($item->is_trial)
                                <span class="badge text-bg-info">Trial</span>
                            @endif
                        </div>

                        <div class="col-12 col-md-2">
                            <div class="small text-secondary">Started</div>
                            <div>{{ optional($item->starts_at)->format('M d, Y') ?? '—' }}</div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="small text-secondary">Renews / Ends</div>
                            <div>
                                @if ($item->ends_at)
                                    {{ $item->ends_at->format('M d, Y') }}
                                @elseif ($item->trial_ends_at)
                                    Trial ends {{ $item->trial_ends_at->format('M d, Y') }}
                                @elseif ($item->renews_at)
                                    {{ $item->renews_at->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($item->admin_note)
                        <div class="mt-2 small text-secondary">
                            Note: {{ $item->admin_note }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-secondary">No billing history yet.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $history->links() }}
    </div>
</div>
@endsection
