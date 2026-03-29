@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Creator Subscription Admin</h1>
            <p class="text-secondary mb-0">Assign, review, and revoke creator posting access.</p>
        </div>

        <form method="GET" action="{{ route('admin.creator-subscriptions.index') }}" class="w-100 w-lg-auto">
            <div class="input-group">
                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search creators...">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Assign Creator Plan Manually</h2>

            <form method="POST" action="{{ route('admin.creator-subscriptions.assign') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-lg-3">
                        <label class="form-label">Creator User ID</label>
                        <input type="number" name="user_id" class="form-control" required>
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Plan</label>
                        <select name="creator_platform_plan_id" class="form-select" required>
                            <option value="">Select plan</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->name }} - ${{ number_format($plan->price / 100, 2) }}/{{ $plan->interval }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-lg-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="trialing">Trialing</option>
                        </select>
                    </div>

                    <div class="col-12 col-lg-2">
                        <label class="form-label">Trial Days</label>
                        <input type="number" name="trial_days" class="form-control" min="0" max="365" value="7">
                    </div>

                    <div class="col-12 col-lg-2">
                        <label class="form-label">Admin Note</label>
                        <input type="text" name="admin_note" class="form-control">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            Assign Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Creator</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Posting Access</th>
                            <th>Trial</th>
                            <th>Ends / Renews</th>
                            <th>Action</th>
                            <th class="pe-4">Sub ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($creators as $creator)
                            @php
                                $sub = $creator->latestCreatorPlatformSubscription;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $creator->name }}</div>
                                    <div class="small text-secondary">{{ $creator->email }}</div>
                                    @if(!empty($creator->username))
                                        <div class="small text-secondary">{{ '@' . $creator->username }}</div>
                                    @endif
                                </td>

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
                                    @if ($creator->canCreateCreatorPosts())
                                        <span class="badge text-bg-success">Enabled</span>
                                    @else
                                        <span class="badge text-bg-danger">Disabled</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($sub?->is_trial)
                                        <span class="badge text-bg-info">
                                            Until {{ optional($sub->trial_ends_at)->format('M d, Y') ?? '—' }}
                                        </span>
                                    @else
                                        —
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

                                <td>
                                    @if ($sub)
                                        <form method="POST" action="{{ route('admin.creator-subscriptions.revoke') }}"
                                              onsubmit="return confirm('Revoke this creator plan now?')">
                                            @csrf
                                            <input type="hidden" name="subscription_id" value="{{ $sub->id }}">
                                            <input type="hidden" name="admin_note" value="Revoked from admin console">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                                                Revoke
                                            </button>
                                        </form>
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="pe-4">
                                    <div class="small text-break">{{ $sub?->stripe_subscription_id ?? 'manual' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-secondary">
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
