@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center gap-3">
    <div class="d-flex align-items-center gap-3">
        @if($otherParticipant?->creatorProfile)
            <img src="{{ $otherParticipant->creatorProfile->avatar_url }}"
                 width="64" height="64" class="rounded-circle" style="object-fit: cover;" alt="">
        @else
            <img src="{{ asset('images/default-avatar.png') }}"
                 width="64" height="64" class="rounded-circle" style="object-fit: cover;" alt="">
        @endif

        <div>
            <h1 class="h3 mb-1">
                {{ $otherParticipant->creatorProfile->display_name ?? $otherParticipant->name ?? 'Conversation' }}
            </h1>
            <div class="text-secondary small">
                {{ $otherParticipant->creatorProfile ? '@'.$otherParticipant->username : $otherParticipant->email }}
            </div>
        </div>
    </div>

    <a href="{{ route('messages.index') }}" class="btn btn-primary btn-sm">Back to Inbox</a>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    <div class="d-grid gap-3 mb-4">
        @foreach($conversation->messages->sortBy('created_at') as $message)
            <div class="d-flex {{ $message->sender_id === $user->id ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="p-3 rounded-4 {{ $message->sender_id === $user->id ? 'bg-primary text-white' : 'bg-dark text-light' }}"
                     style="max-width: 85%; min-width: 180px;">
                    <div class="small fw-semibold mb-1">
                        {{ $message->sender_id === $user->id ? 'You' : $message->sender->name }}
                    </div>
                    <div>{{ $message->body }}</div>
                    <div class="small mt-2 opacity-75">
                        {{ $message->created_at->format('M j, Y g:i A') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('messages.store', $conversation) }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <textarea name="body" rows="4" class="form-control" placeholder="Write your reply..."></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Send Reply</button>
            </div>
        </div>
    </form>
</div>
@endsection
