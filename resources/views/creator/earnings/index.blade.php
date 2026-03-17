@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Earnings</h1>
        <p class="text-secondary mb-0">Track subscription revenue, tips, and payout reports.</p>
    </div>

    <a href="{{ route('creator.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="small text-secondary mb-1">Subscription Revenue</div>
            <div class="display-6 fw-bold">${{ number_format($stats['subscription_revenue'], 2) }}</div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="small text-secondary mb-1">Tip Revenue</div>
            <div class="display-6 fw-bold">${{ number_format($stats['tip_revenue'], 2) }}</div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="bg-panel rounded-4 p-4 h-100">
            <div class="small text-secondary mb-1">Gross Revenue</div>
            <div class="display-6 fw-bold">${{ number_format($stats['gross_revenue'], 2) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Recent Tips</h2>

            @forelse($recentTips as $tip)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3">
                        <div>
                            <div class="fw-bold">{{ $tip->fan->name }}</div>
                            <div class="small text-secondary">{{ $tip->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="fw-bold">${{ number_format($tip->amount, 2) }}</div>
                    </div>

                    @if($tip->message)
                        <div class="small text-light-emphasis mt-2">{{ $tip->message }}</div>
                    @endif
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No successful tips yet.</div>
            @endforelse

            <div class="mt-4">
                {{ $recentTips->links() }}
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="bg-panel rounded-4 p-3 p-md-4 h-100">
            <h2 class="h5 mb-3">Payout Reports</h2>

            @forelse($payoutReports as $report)
                <div class="border rounded-4 p-3 mb-3">
                    <div class="d-flex justify-content-between gap-3 mb-2">
                        <div class="fw-bold">
                            {{ $report->period_start->format('M j, Y') }} - {{ $report->period_end->format('M j, Y') }}
                        </div>
                        <span class="badge {{ $report->status === 'paid' ? 'text-bg-success' : ($report->status === 'approved' ? 'text-bg-primary' : 'text-bg-warning') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>

                    <div class="small text-secondary mb-1">Net Creator Amount</div>
                    <div class="fw-bold mb-2">${{ number_format($report->net_creator_amount, 2) }}</div>

                    @if($report->notes)
                        <div class="small text-light-emphasis">{{ $report->notes }}</div>
                    @endif
                </div>
            @empty
                <div class="alert alert-secondary mb-0">No payout reports yet.</div>
            @endforelse

            <div class="mt-4">
                {{ $payoutReports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
