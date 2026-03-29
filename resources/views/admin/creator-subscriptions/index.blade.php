@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Creator Posting Access</h1>
            <p class="text-secondary mb-0">
                View creator subscription status and posting eligibility.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.creator-subscriptions.index') }}" class="w-100 w-lg-auto">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Search creators..."
                >
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Creator</th>
                            <th>Email</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Posting Access</th>
                            <th>Renews / Ends</th>
                            <th class="pe-4">Stripe Sub ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($creators as $creator)
                            @php
                                $sub = $creator->latestCreatorPlatformSubscription;
                                $canPost = $creator->canCreateCreatorPosts();
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $creator->name }}</div>
                                    @if(!empty($creator->username))
                                        <div class="small text-secondary">{{ '@' . $creator->username }}</div>
                                    @endif
                                </td>
                                <td>{{ $creator->email }}</td>
                                <td>{{ $sub?->plan?->name ?? '—' }}</td>
                                <td>
                                    @if ($sub)
                                        <span class="badge text-bg-{{ $sub->statusBadgeClass() }}">
                                            {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($canPost)
                                        <span class="badge text-bg-success">Enabled</span>
                                    @else
                                        <span class="badge text-bg-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sub?->ends_at)
                                        {{ $sub->ends_at->format('M d, Y') }}
                                    @elseif ($sub?->renews_at)
                                        {{ $sub->renews_at->format('M d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div class="small text-break">{{ $sub?->stripe_subscription_id ?? '—' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">
                                    No creators found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $creators->links() }}
    </div>
</div>
@endsection
