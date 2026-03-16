<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Post;
use App\Models\Plf_subscription;
use App\Models\Tip;
use App\Models\User;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users_total' => User::count(),
            'creators_total' => User::where('role', 'creator')->count(),
            'fans_total' => User::where('role', 'fan')->count(),
            'posts_total' => Post::count(),
            'comments_total' => Comment::count(),
            'conversations_total' => Conversation::count(),
            'messages_total' => Message::count(),
            'active_subscriptions_total' => Plf_subscription::where('status', 'active')->count(),
            'tips_total_amount' => Tip::where('status', 'succeeded')->sum('amount'),
            'tips_total_count' => Tip::where('status', 'succeeded')->count(),
        ];

        $topCreatorsByTips = User::query()
            ->where('role', 'creator')
            ->with('creatorProfile')
            ->get()
            ->map(function ($creator) {
                $creator->tips_sum = $creator->tipsReceived()->where('status', 'succeeded')->sum('amount');
                return $creator;
            })
            ->sortByDesc('tips_sum')
            ->take(10);

        $topCreatorsBySubscribers = User::query()
            ->where('role', 'creator')
            ->with('creatorProfile')
            ->get()
            ->map(function ($creator) {
                $creator->subscribers_count_metric = $creator->incomingSubscriptions()->where('status', 'active')->count();
                return $creator;
            })
            ->sortByDesc('subscribers_count_metric')
            ->take(10);

        return view('admin.analytics.index', compact(
            'stats',
            'topCreatorsByTips',
            'topCreatorsBySubscribers'
        ));
    }
}
