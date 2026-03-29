<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Recent Billing Activity</h2>
                <p class="text-secondary mb-0">Your latest creator subscription records.</p>
            </div>

            <div>
                <a href="{{ route('creator.billing.history') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    Full History
                </a>
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

                    <div class="col-6 col-md-2">
                        @if($item->is_trial)
                            <span class="badge text-bg-info">Trial</span>
                        @endif
                    </div>

                    <div class="col-12 col-md-4">
                        @if ($item->ends_at)
                            <div>Ends {{ $item->ends_at->format('M d, Y') }}</div>
                        @elseif ($item->trial_ends_at)
                            <div>Trial ends {{ $item->trial_ends_at->format('M d, Y') }}</div>
                        @elseif ($item->renews_at)
                            <div>Renews {{ $item->renews_at->format('M d, Y') }}</div>
                        @else
                            <div>—</div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-secondary">No subscription history yet.</div>
        @endforelse
    </div>
</div>
