<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;

class CreatorBillingController extends Controller
{
    public function show(Request $request): View
    {
        $plans = CreatorPlatformPlan::where('is_active', true)->get();
        $subscription = $request->user()->latestCreatorPlatformSubscription;

        return view('creator.billing.subscribe', compact('plans', 'subscription'));
    }

    public function checkout(Request $request, CreatorPlatformPlan $plan): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->is_creator, 403);

        if (!$plan->is_active || !$plan->stripe_price_id) {
            return back()->with('error', 'This creator plan is not available.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'metadata' => [
                'type' => 'creator_platform_subscription',
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'success_url' => route('creator.billing.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creator.billing.cancel', [], true),
        ]);
        return redirect()->away($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        $user = $request->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->retrieve(
            $request->string('session_id')->toString(),
            []
        );

        if (($session->payment_status ?? null) !== 'paid' && empty($session->subscription)) {
            return redirect()
                ->route('creator.billing.subscribe')
                ->with('error', 'Payment was not completed.');
        }

        $planId = data_get($session, 'metadata.plan_id');
        $plan = CreatorPlatformPlan::findOrFail($planId);

        $stripeSubscription = null;

        if (!empty($session->subscription)) {
            $stripeSubscription = $stripe->subscriptions->retrieve($session->subscription, []);
        }

        CreatorPlatformSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'stripe_subscription_id' => $session->subscription,
            ],
            [
                'creator_platform_plan_id' => $plan->id,
                'provider' => 'stripe',
                'stripe_checkout_session_id' => $session->id,
                'stripe_customer_id' => $session->customer ?? null,
                'status' => $stripeSubscription->status ?? 'active',
                'starts_at' => now(),
                'renews_at' => isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'ends_at' => null,
                'meta' => [
                    'session' => $session,
                    'subscription' => $stripeSubscription,
                ],
            ]
        );

        return redirect()
            ->route('creator.posts.index')
            ->with('success', 'Your creator subscription is active. You can now create posts.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()
            ->route('creator.billing.subscribe')
            ->with('error', 'Checkout was canceled.');
    }
}
