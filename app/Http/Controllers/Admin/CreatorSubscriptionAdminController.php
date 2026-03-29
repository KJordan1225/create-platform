<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorSubscriptionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));

        $creators = User::query()
            ->where('is_creator', true)
            ->with([
                'latestCreatorPlatformSubscription.plan',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.creator-subscriptions.index', compact('creators', 'search'));
    }
}
