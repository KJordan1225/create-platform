<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartConversationRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Conversation;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AbuseDetectionService;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = $user->allConversations()
            ->with([
                'creator.creatorProfile',
                'fan',
                'messages' => fn ($q) => $q->latest()->limit(1),
            ])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('messages.index', compact('conversations', 'user'));
    }

    public function show(Request $request, Conversation $conversation, MessagingService $messagingService): View
    {
        $this->authorize('view', $conversation);

        $conversation->load([
            'creator.creatorProfile',
            'fan',
            'messages.sender',
        ]);

        $messagingService->markConversationReadFor($conversation, $request->user());

        return view('messages.show', [
            'conversation' => $conversation,
            'user' => $request->user(),
            'otherParticipant' => $conversation->otherParticipant($request->user()),
        ]);
    }

    public function start(
        StartConversationRequest $request,
        User $creator,
        MessagingService $messagingService,
        AbuseDetectionService $abuseDetectionService
    ) 

    {
        abort_unless($creator->isApprovedCreator() && $creator->is_active, 404);
        abort_if($request->user()->id === $creator->id, 403, 'You cannot message yourself.');

        if ($abuseDetectionService->isMessageSpam($request->user(), $request->validated('body'))) {
            return back()->withErrors([
                'body' => 'Your message was flagged as suspicious. Please revise and try again.',
            ]);
        }

        $conversation = $messagingService->startOrGetConversation($request->user(), $creator);
        $messagingService->sendMessage($conversation, $request->user(), $request->validated('body'));

        return redirect()
            ->route('messages.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }

    public function store(
        StoreMessageRequest $request,
        Conversation $conversation,
        MessagingService $messagingService,
        AbuseDetectionService $abuseDetectionService
    ) {
        $this->authorize('sendMessage', $conversation);

        if ($abuseDetectionService->isMessageSpam($request->user(), $request->validated('body'))) {
            return back()->withErrors([
                'body' => 'Your message was flagged as suspicious. Please revise and try again.',
            ]);
        }

        $messagingService->sendMessage($conversation, $request->user(), $request->validated('body'));

        return back()->with('success', 'Reply sent successfully.');
    }

}
