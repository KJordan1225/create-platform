<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $subscriptions = $user->outgoingSubscriptions()
            ->with(['creator.creatorProfile'])
            ->latest()
            ->get();

        $tips = $user->tipsSent()
            ->with(['creator.creatorProfile'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact('user', 'subscriptions', 'tips'));
    }
}
