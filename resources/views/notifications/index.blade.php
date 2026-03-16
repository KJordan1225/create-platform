@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Notifications</h1>
        <p class="text-secondary mb-0">Recent platform and message alerts.</p>
    </div>

    <form method="POST" action="{{ route('notifications.read_all') }}">
        @csrf
        <button class="btn btn-outline-light">Mark All Read</button>
    </form>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    @forelse($notifications as $notification)
        <div class="border rounded-4 p-3 mb-3 {{ is_null($notification->read_at) ? 'border-primary' : '' }}">
            <div class="d-flex justify-content-between gap-3">
                <div>
                    <div class="fw-bold">
                        {{ $notification->data['sender_name'] ?? 'Notification' }}
                    </div>
                    <div class="text-light-emphasis small">
                        {{ $notification->data['body_preview'] ?? 'New activity on your account.' }}
                    </div>
                </div>

                <div class="text-end">
                    @if(is_null($notification->read_at))
                        <span class="badge text-bg-primary mb-2">Unread</span>
                    @endif
                    <div class="small text-secondary">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            </div>

            @if(($notification->data['type'] ?? null) === 'new_message' && !empty($notification->data['conversation_id']))
                <div class="mt-3">
                    <a href="{{ route('messages.show', $notification->data['conversation_id']) }}" class="btn btn-sm btn-outline-light">
                        Open Conversation
                    </a>
                </div>
            @endif
        </div>
    @empty
        <div class="alert alert-secondary mb-0">No notifications yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
