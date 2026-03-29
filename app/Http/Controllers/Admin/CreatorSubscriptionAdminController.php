<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorSubscriptionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));

        $creators = User::query()
            ->where('is_creator', true)
            ->with(['latestCreatorPlatformSubscription.plan'])
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

        $plans = CreatorPlatformPlan::where('is_active', true)->orderBy('price')->get();

        return view('admin.creator-subscriptions.index', compact('creators', 'plans', 'search'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'creator_platform_plan_id' => ['required', 'integer', 'exists:creator_platform_plans,id'],
            'status' => ['required', 'in:active,trialing'],
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::findOrFail($data['user_id']);
        abort_unless($user->is_creator, 422, 'Selected user is not a creator.');

        $plan = CreatorPlatformPlan::findOrFail($data['creator_platform_plan_id']);

        CreatorPlatformSubscription::create([
            'user_id' => $user->id,
            'creator_platform_plan_id' => $plan->id,
            'provider' => 'manual',
            'status' => $data['status'],
            'is_trial' => $data['status'] === 'trialing',
            'trial_ends_at' => $data['status'] === 'trialing' && !empty($data['trial_days'])
                ? now()->addDays((int) $data['trial_days'])
                : null,
            'starts_at' => now(),
            'renews_at' => $data['status'] === 'active' ? now()->addMonth() : null,
            'assigned_by' => $request->user()->id,
            'admin_note' => $data['admin_note'] ?? null,
            'meta' => [
                'assigned_manually' => true,
            ],
        ]);

        return back()->with('success', 'Creator plan assigned successfully.');
    }

    public function revoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subscription_id' => ['required', 'integer', 'exists:creator_platform_subscriptions,id'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $subscription = CreatorPlatformSubscription::findOrFail($data['subscription_id']);

        $subscription->update([
            'status' => 'canceled',
            'revoked_at' => now(),
            'ends_at' => now(),
            'admin_note' => $data['admin_note'] ?? $subscription->admin_note,
            'meta' => array_merge($subscription->meta ?? [], [
                'revoked_manually' => true,
            ]),
        ]);

        return back()->with('success', 'Creator plan revoked.');
    }
}
