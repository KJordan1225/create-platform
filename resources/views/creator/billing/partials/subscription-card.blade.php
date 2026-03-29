@php
    $creatorSubscription = auth()->user()->latestCreatorPlatformSubscription;
    $postingUnlocked = auth()->user()->hasActiveCreatorPlatformSubscription();
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <div class="text-uppercase small fw-semibold mb-1 {{ $postingUnlocked ? 'text-success' : 'text-warning' }}">
                    Creator Posting Access
                </div>

                <h2 class="h5 mb-1">
                    {{ $postingUnlocked ? 'Posting unlocked' : 'Posting locked' }}
                </h2>

                @if ($postingUnlocked)
                    <p class="text-secondary mb-2">
                        Your creator subscription is active.
                        @if ($creatorSubscription?->willCancelAtPeriodEnd() && $creatorSubscription?->ends_at)
                            It will end on {{ $creatorSubscription->ends_at->format('M d, Y') }} unless reactivated.
                        @elseif ($creatorSubscription?->renews_at)
                            Renews on {{ $creatorSubscription->renews_at->format('M d, Y') }}.
                        @endif
                    </p>
                    <span class="badge text-bg-{{ $creatorSubscription?->statusBadgeClass() ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $creatorSubscription?->status ?? 'unknown')) }}
                    </span>
                @else
                    <p class="text-secondary mb-0">
                        Activate your creator subscription to publish new posts and locked content.
                    </p>
                @endif
            </div>

            <div class="d-flex flex-column flex-sm-row gap-2">
                @if ($postingUnlocked)
                    <a href="{{ route('creator.posts.create') }}" class="btn btn-primary rounded-pill px-4">
                        Create Post
                    </a>
                    <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        Manage Billing
                    </a>
                @else
                    <a href="{{ route('creator.billing.subscribe') }}" class="btn btn-warning rounded-pill px-4">
                        Unlock Posting
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
