<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorProfile;
use App\Models\Subscription;
use App\Models\Tip;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users_count' => User::count(),
            'creators_count' => User::where('role', 'creator')->count(),
            'approved_creators_count' => User::where('role', 'creator')->whereNotNull('creator_approved_at')->count(),
            'pending_creators_count' => User::where('role', 'creator')->whereNull('creator_approved_at')->count(),
            'active_subscriptions_count' => Subscription::where('status', 'active')->count(),
            'tips_total' => Tip::where('status', 'succeeded')->sum('amount'),
        ];

        $pendingCreators = User::query()
            ->where('role', 'creator')
            ->whereNull('creator_approved_at')
            ->with('creatorProfile')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingCreators'));
    }
}
