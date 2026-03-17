<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FanSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = $request->user()
            ->outgoingSubscriptions()
            ->with('creator.creatorProfile')
            ->latest()
            ->paginate(20);

        return view('subscriptions.index', compact('subscriptions'));
    }
}
