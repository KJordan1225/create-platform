<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StripeCreatorBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Mail\CreatorApprovedMail;
use Illuminate\Support\Facades\Mail;

class CreatorManagementController extends Controller
{
    public function index(): View
    {
        $creators = User::query()
            ->where('role', 'creator')
            ->with('creatorProfile')
            ->latest()
            ->paginate(20);

        return view('admin.creators.index', compact('creators'));
    }

    public function show(User $user): View
    {
        abort_unless($user->role === 'creator', 404);

        $user->load(['creatorProfile', 'posts.media', 'incomingSubscriptions']);

        return view('admin.creators.show', ['creator' => $user]);
    }

    public function approve(User $user, StripeCreatorBillingService $billingService): RedirectResponse
    {
        abort_unless($user->role === 'creator' && $user->creatorProfile, 404);

        $user->update([
            'is_active' => true,
            'is_creator' => true,
            'creator_approved_at' => now(),
        ]);        

        $user->creatorProfile->update([
            'is_published' => true,
        ]);      

        $billingService->syncCreatorSubscriptionPrice($user->creatorProfile);

        Mail::to($user->email)->queue(new CreatorApprovedMail($user));

        return back()->with('success', 'Creator approved successfully.');
    }

    public function suspend(User $user): RedirectResponse
    {
        abort_unless($user->role === 'creator', 404);

        $user->update([
            'is_active' => false,
        ]);

        if ($user->creatorProfile) {
            $user->creatorProfile->update([
                'is_published' => false,
            ]);
        }

        return back()->with('success', 'Creator suspended successfully.');
    }

    public function reactivate(User $user): RedirectResponse
    {
        abort_unless($user->role === 'creator', 404);

        $user->update([
            'is_active' => true,
        ]);

        if ($user->creatorProfile && $user->creator_approved_at) {
            $user->creatorProfile->update([
                'is_published' => true,
            ]);
        }

        return back()->with('success', 'Creator reactivated successfully.');
    }
}
