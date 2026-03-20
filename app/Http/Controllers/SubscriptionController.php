<?php

namespace App\Http\Controllers;

use App\Models\Plf_subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function showCheckout(User $creator)
    {
        abort_unless($creator->isApprovedCreator(), 404);

        $profile = $creator->creatorProfile;

        return view('subscriptions.checkout', compact('creator', 'profile'));
    }

    public function checkout(Request $request, User $creator)
    {
        abort_unless($creator->isApprovedCreator(), 404);

        $fan = $request->user();

        abort_if($fan->id === $creator->id, 403, 'You cannot subscribe to yourself.');

        if (
            empty($creator->stripe_account_id) ||
            !$creator->stripe_charges_enabled ||
            !$creator->stripe_payouts_enabled ||
            $creator->stripe_onboarding_status !== 'connected'
        ) {
            return back()->withErrors([
                'subscription' => 'This creator is not ready to receive payouts yet.',
            ]);
        }

        $profile = $creator->creatorProfile;

        if (!$profile || !$profile->stripe_price_id) {
            return back()->withErrors([
                'subscription' => 'This creator is not ready for subscriptions yet.',
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'subscription',
            'customer_email' => $fan->email,
            'line_items' => [[
                'price' => $profile->stripe_price_id,
                'quantity' => 1,
            ]],
            'success_url' => route('subscriptions.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creators.show', $profile->slug),
            'metadata' => [
                'fan_id' => $fan->id,
                'creator_id' => $creator->id,
                'type' => 'creator_subscription',
            ],
        ]);

        Plf_subscription::updateOrCreate(
            [
                'fan_id' => $fan->id,
                'creator_id' => $creator->id,
            ],
            [
                'stripe_checkout_session_id' => $session->id,
                'amount' => $profile->monthly_price,
                'currency' => config('cashier.currency', 'usd'),
                'status' => 'pending',
            ]
        );

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['subscription' => 'Missing session ID.']);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            $fanId = $session->metadata->fan_id ?? null;
            $creatorId = $session->metadata->creator_id ?? null;

            if ($fanId && $creatorId) {
                Plf_subscription::where('fan_id', $fanId)
                    ->where('creator_id', $creatorId)
                    ->update([
                        'status' => 'active',
                        'stripe_subscription_id' => $session->subscription ?? null,
                        'updated_at' => now(),
                    ]);
            }

        } catch (\Throwable $e) {
            // Optional: log error
            \Log::error('Stripe success error: ' . $e->getMessage());
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your subscription is now active.');
    }

    public function cancel(Request $request, User $creator)
    {
        $subscription = Plf_subscription::query()
            ->where('fan_id', $request->user()->id)
            ->where('creator_id', $creator->id)
            ->firstOrFail();

        if ($subscription->stripe_subscription_id) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
                $stripeSubscription->cancel();
            } catch (\Throwable $e) {
                // keep going so local state can still be updated
            }
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'ends_at' => now(),
        ]);

        return back()->with('success', 'Subscription canceled.');
    }

}
