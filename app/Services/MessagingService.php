<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function startOrGetConversation(User $fan, User $creator): Conversation
    {
        return Conversation::firstOrCreate(
            [
                'creator_id' => $creator->id,
                'fan_id' => $fan->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );
    }

    public function sendMessage(Conversation $conversation, User $sender, string $body): Message
    {
        return DB::transaction(function () use ($conversation, $sender, $body) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'body' => $body,
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            $recipient = $conversation->otherParticipant($sender);

            if ($recipient) {
                $recipient->notify(new NewMessageNotification($conversation, $message, $sender));
            }

            return $message;
        });
    }

    public function markConversationReadFor(Conversation $conversation, User $user): void
    {
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $user->id)
            ->update([
                'read_at' => now(),
            ]);
    }
}
