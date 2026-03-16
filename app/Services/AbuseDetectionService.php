<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Post;
use App\Models\Tip;
use App\Models\User;

class AbuseDetectionService
{
    public function isMessageSpam(User $sender, string $body): bool
    {
        $recentCount = Message::query()
            ->where('sender_id', $sender->id)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($recentCount >= 10) {
            return true;
        }

        $body = trim(mb_strtolower($body));

        $spamPhrases = [
            'cashapp me',
            'telegram me',
            'whatsapp me',
            'send crypto',
            'btc only',
            'click this link',
        ];

        foreach ($spamPhrases as $phrase) {
            if (str_contains($body, $phrase)) {
                return true;
            }
        }

        return false;
    }

    public function isCommentSpam(User $sender, string $body): bool
    {
        $recentCount = $sender->comments()
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        if ($recentCount >= 8) {
            return true;
        }

        return preg_match('/https?:\/\//i', $body) === 1;
    }

    public function isTipAbuse(User $fan, float $amount): bool
    {
        $recentLargeTips = Tip::query()
            ->where('fan_id', $fan->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('amount', '>=', 100)
            ->count();

        if ($amount >= 100 && $recentLargeTips >= 3) {
            return true;
        }

        return false;
    }
}
