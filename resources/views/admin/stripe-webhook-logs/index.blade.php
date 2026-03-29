@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Stripe Webhook Logs</h1>
            <p class="text-secondary mb-0">Review incoming webhook events and processing results.</p>
        </div>

        <form method="GET" action="{{ route('admin.webhook-logs.index') }}" class="row g-2">
            <div class="col-12 col-md-auto">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Search event type, ID, message..."
                >
            </div>

            <div class="col-12 col-md-auto">
                <select name="processed" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ (string) $processed === '1' ? 'selected' : '' }}>Processed</option>
                    <option value="0" {{ (string) $processed === '0' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="col-12 col-md-auto">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Received</th>
                            <th>Event Type</th>
                            <th>Event ID</th>
                            <th>Status</th>
                            <th>HTTP</th>
                            <th>Message</th>
                            <th class="pe-4">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4">{{ optional($log->received_at)->format('M d, Y g:i A') ?? '—' }}</td>
                                <td>{{ $log->event_type ?? '—' }}</td>
                                <td><span class="small text-break">{{ $log->event_id ?? '—' }}</span></td>
                                <td>
                                    @if($log->processed)
                                        <span class="badge text-bg-success">Processed</span>
                                    @else
                                        <span class="badge text-bg-danger">Failed</span>
                                    @endif
                                </td>
                                <td>{{ $log->http_status }}</td>
                                <td><span class="small">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</span></td>
                                <td class="pe-4">
                                    <a href="{{ route('admin.webhook-logs.show', $log) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        Open
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-secondary">No webhook logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
