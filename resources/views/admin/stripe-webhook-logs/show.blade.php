@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1">Webhook Log Detail</h1>
            <p class="text-secondary mb-0">Inspect the full payload and processing result.</p>
        </div>

        <a href="{{ route('admin.webhook-logs.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            Back to Logs
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="small text-secondary">Event Type</div>
                    <div>{{ $log->event_type ?? '—' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-secondary">Event ID</div>
                    <div class="text-break">{{ $log->event_id ?? '—' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-secondary">Processed</div>
                    <div>{{ $log->processed ? 'Yes' : 'No' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-secondary">HTTP Status</div>
                    <div>{{ $log->http_status }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-secondary">Received</div>
                    <div>{{ optional($log->received_at)->format('M d, Y g:i A') ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <div class="small text-secondary">Message</div>
                    <div>{{ $log->message ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Headers</h2>
            <pre class="bg-light p-3 rounded-3 small mb-0">{{ json_encode($log->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Payload</h2>
            <pre class="bg-light p-3 rounded-3 small mb-0">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>
</div>
@endsection
