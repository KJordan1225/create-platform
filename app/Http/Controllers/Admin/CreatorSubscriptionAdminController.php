<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use App\Models\CreatorSubscriptionAudit;
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

        $subscription = CreatorPlatformSubscription::create([
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

        CreatorSubscriptionAudit::create([
            'creator_platform_subscription_id' => $subscription->id,
            'user_id' => $user->id,
            'admin_user_id' => $request->user()->id,
            'action' => 'assigned',
            'note' => $data['admin_note'] ?? 'Assigned manually from admin console.',
            'new_values' => $subscription->only([
                'status',
                'is_trial',
                'trial_ends_at',
                'starts_at',
                'renews_at',
            ]),
            'meta' => [
                'provider' => 'manual',
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

        $old = $subscription->only([
            'status',
            'revoked_at',
            'ends_at',
            'admin_note',
        ]);

        $subscription->update([
            'status' => 'canceled',
            'revoked_at' => now(),
            'ends_at' => now(),
            'admin_note' => $data['admin_note'] ?? $subscription->admin_note,
            'meta' => array_merge($subscription->meta ?? [], [
                'revoked_manually' => true,
            ]),
        ]);

        CreatorSubscriptionAudit::create([
            'creator_platform_subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'admin_user_id' => $request->user()->id,
            'action' => 'revoked',
            'note' => $data['admin_note'] ?? 'Revoked manually from admin console.',
            'old_values' => $old,
            'new_values' => $subscription->fresh()->only([
                'status',
                'revoked_at',
                'ends_at',
                'admin_note',
            ]),
            'meta' => [
                'provider' => $subscription->provider,
            ],
        ]);

        return back()->with('success', 'Creator plan revoked.');
    }
}
