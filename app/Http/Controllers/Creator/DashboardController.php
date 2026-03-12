<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $creator = auth()->user();

        $postCount = $creator->posts()->count();
        $subscriberCount = $creator->incomingSubscriptions()
            ->where('status', 'active')
            ->count();
        $tipTotal = $creator->tipsReceived()
            ->where('status', 'succeeded')
            ->sum('amount');

        $recentSubscribers = $creator->incomingSubscriptions()
            ->with('fan')
            ->latest()
            ->take(10)
            ->get();

        $recentTips = $creator->tipsReceived()
            ->with('fan')
            ->latest()
            ->take(10)
            ->get();

        return view('creator.dashboard', compact(
            'creator',
            'postCount',
            'subscriberCount',
            'tipTotal',
            'recentSubscribers',
            'recentTips'
        ));
    }
}
