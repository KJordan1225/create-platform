@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 mb-1">Reports</h1>
    <p class="text-secondary mb-0">Review submitted reports across creators, posts, and comments.</p>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    @forelse($reports as $report)
        <div class="border rounded-4 p-3 mb-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <div class="fw-bold">{{ ucfirst($report->reason) }}</div>
                    <div class="small text-secondary mb-2">
                        Submitted by {{ $report->user->name }} · {{ $report->created_at->diffForHumans() }}
                    </div>

                    <div class="mb-2">
                        <span class="badge text-bg-dark">{{ class_basename($report->reportable_type) }}</span>
                        <span class="badge {{ $report->status === 'open' ? 'text-bg-warning' : ($report->status === 'resolved' ? 'text-bg-success' : 'text-bg-secondary') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>

                    @if($report->details)
                        <div class="text-light-emphasis">{{ $report->details }}</div>
                    @endif
                </div>

                <div class="d-grid gap-2">
                    @if($report->status === 'open')
                        <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                            @csrf
                            <button class="btn btn-success btn-sm w-100">Resolve</button>
                        </form>

                        <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                            @csrf
                            <button class="btn btn-outline-secondary btn-sm w-100">Dismiss</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary mb-0">No reports found.</div>
    @endforelse

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
</div>
@endsection
