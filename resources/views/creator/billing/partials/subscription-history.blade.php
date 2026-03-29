<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h5 mb-1">Subscription History</h2>
                <p class="text-secondary mb-0">Recent creator plan billing activity.</p>
            </div>
        </div>

        @forelse ($history as $item)
            <div class="py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="fw-semibold">{{ $item->plan->name ?? 'Creator Plan' }}</div>
                        <div class="text-secondary small">
                            Started {{ optional($item->starts_at)->format('M d, Y') ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <span class="badge text-bg-{{ $item->statusBadgeClass() }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="small text-secondary">Renews / Ends</div>
                        <div>
                            @if ($item->ends_at)
                                {{ $item->ends_at->format('M d, Y') }}
                            @elseif ($item->renews_at)
                                {{ $item->renews_at->format('M d, Y') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <div class="small text-secondary">Stripe Subscription</div>
                        <div class="text-break small">{{ $item->stripe_subscription_id ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-secondary">No subscription history yet.</div>
        @endforelse
    </div>
</div>
