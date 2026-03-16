@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h2 mb-1">Messages</h1>
    <p class="text-secondary mb-0">Your conversations with creators and fans.</p>
</div>

<div class="bg-panel rounded-4 p-3 p-md-4">
    @forelse($conversations as $conversation)
        @php
            $other = $conversation->otherParticipant($user);
            $latestMessage = $conversation->messages->first();
            $unreadCount = $conversation->unreadCountFor($user);
        @endphp

        <a href="{{ route('messages.show', $conversation) }}" class="text-decoration-none text-reset">
            <div class="border rounded-4 p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if($other?->creatorProfile)
                            <img src="{{ $other->creatorProfile->avatar_url }}"
                                 width="56" height="56" class="rounded-circle" style="object-fit: cover;" alt="">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}"
                                 width="56" height="56" class="rounded-circle" style="object-fit: cover;" alt="">
                        @endif

                        <div>
                            <div class="fw-bold">
                                {{ $other->creatorProfile->display_name ?? $other->name ?? 'Unknown User' }}
                            </div>
                            <div class="small text-secondary">
                                {{ $latestMessage?->body ? \Illuminate\Support\Str::limit($latestMessage->body, 90) : 'No messages yet.' }}
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        @if($unreadCount > 0)
                            <span class="badge text-bg-primary mb-2">{{ $unreadCount }} new</span>
                        @endif

                        <div class="small text-secondary">
                            {{ $conversation->last_message_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="alert alert-secondary mb-0">No conversations yet.</div>
    @endforelse

    <div class="mt-4">
        {{ $conversations->links() }}
    </div>
</div>
@endsection
