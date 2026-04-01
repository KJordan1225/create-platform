<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class CreatorBillingController extends Controller
{
    public function show(Request $request): View
    {
        $plans = CreatorPlatformPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $subscription = $request->user()->latestCreatorPlatformSubscription;
        $history = $request->user()->creatorPlatformSubscriptions()
            ->with(['plan', 'assigner'])
            ->latest('id')
            ->paginate(15);

        return view('creator.billing.subscribe', compact('plans', 'subscription', 'history'));
    }

    public function history(Request $request): View
    {
        $history = $request->user()->creatorPlatformSubscriptions()
            ->with(['plan', 'assigner'])
            ->latest('id')
            ->paginate(20);

        return view('creator.billing.history', compact('history'));
    }

    public function checkout(Request $request, CreatorPlatformPlan $plan): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->is_creator, 403);

        if (!$plan->is_active || blank($plan->stripe_price_id)) {
            return back()->with('error', 'This creator plan is not available right now.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $subscriptionData = [
            'metadata' => [
                'type' => 'creator_platform_subscription',
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
        ];

        if ($plan->has_trial && $plan->trial_days > 0) {
            $subscriptionData['trial_period_days'] = $plan->trial_days;
        }

        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'subscription_data' => $subscriptionData,
            'metadata' => [
                'type' => 'creator_platform_subscription',
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
            'success_url' => route('creator.billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creator.billing.cancel'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $user = $request->user();
        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve($validated['session_id'], []);
        $plan = CreatorPlatformPlan::findOrFail((int) ($session->metadata->plan_id ?? 0));

        $stripeSubscription = null;

        if (!empty($session->subscription)) {
            $stripeSubscription = $stripe->subscriptions->retrieve($session->subscription, []);
        }

        CreatorPlatformSubscription::updateOrCreate(
            [
                'stripe_subscription_id' => $session->subscription,
            ],
            [
                'user_id' => $user->id,
                'creator_platform_plan_id' => $plan->id,
                'provider' => 'stripe',
                'stripe_checkout_session_id' => $session->id,
                'stripe_customer_id' => $session->customer ?? null,
                'status' => $stripeSubscription->status ?? 'active',
                'is_trial' => ($stripeSubscription->status ?? null) === 'trialing',
                'trial_ends_at' => isset($stripeSubscription->trial_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                    : null,
                'starts_at' => now(),
                'renews_at' => isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'ends_at' => !empty($stripeSubscription->cancel_at_period_end) && isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'canceled_at' => !empty($stripeSubscription->canceled_at)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                    : null,
                'meta' => [
                    'checkout_session_id' => $session->id,
                    'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                ],
            ]
        );

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Your creator subscription is active.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()
            ->route('creator.billing.subscribe')
            ->with('error', 'Subscription checkout was canceled.');
    }

    public function portal(Request $request): RedirectResponse
    {
        $subscription = $request->user()->latestCreatorPlatformSubscription;

        if (!$subscription || blank($subscription->stripe_customer_id)) {
            return back()->with('error', 'No billing portal is available for this account.');
        }

        $portal = BillingPortalSession::create([
            'customer' => $subscription->stripe_customer_id,
            'return_url' => route('creator.billing.subscribe', [], false),
        ]);

        return redirect()->away($portal->url);
    }

    public function cancelAtPeriodEnd(Request $request): RedirectResponse
    {
        $subscription = $request->user()->latestCreatorPlatformSubscription;

        if (!$subscription || blank($subscription->stripe_subscription_id)) {
            return back()->with('error', 'No Stripe-backed creator subscription was found.');
        }

        if (!$subscription->isActive()) {
            return back()->with('error', 'Only active subscriptions can be scheduled for cancellation.');
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        $updated = $stripe->subscriptions->update($subscription->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);

        $subscription->update([
            'status' => $updated->status ?? $subscription->status,
            'ends_at' => isset($updated->current_period_end)
                ? \Carbon\Carbon::createFromTimestamp($updated->current_period_end)
                : $subscription->ends_at,
            'meta' => array_merge($subscription->meta ?? [], [
                'cancel_at_period_end' => true,
                'last_action' => 'cancel_at_period_end',
            ]),
        ]);

        return back()->with('success', 'Your subscription will cancel at the end of the current billing period.');
    }
}
