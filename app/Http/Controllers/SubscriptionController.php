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
            !$creator->stripe_charges_enabled ||
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

        if (empty($creator->stripe_account_id)) {
            return back()->withErrors([
                'subscription' => 'This creator is not connected for payouts yet.',
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

            'subscription_data' => [
                'application_fee_percent' => 20,
                'transfer_data' => [
                    'destination' => $creator->stripe_account_id,
                ],
                'metadata' => [
                    'fan_id' => (string) $fan->id,
                    'creator_id' => (string) $creator->id,
                    'type' => 'creator_subscription',
                ],
            ],

            'success_url' => route('subscriptions.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creators.show', $profile->slug),

            'metadata' => [
                'fan_id' => (string) $fan->id,
                'creator_id' => (string) $creator->id,
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
                'stripe_account_destination' => $creator->stripe_account_id,
                'application_fee_percent' => 20.00,
                'amount' => $profile->monthly_price,
                'currency' => config('cashier.currency', 'usd'),
                'status' => 'pending',
            ]
        );

        return redirect($session->url);

    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        abort_unless($sessionId, 404);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve([
            'id' => $sessionId,
            'expand' => ['subscription'],
        ]);

        $fanId = data_get($session, 'metadata.fan_id');
        $creatorId = data_get($session, 'metadata.creator_id');

        abort_unless($fanId && $creatorId, 404);

        $subscription = Plf_subscription::where('fan_id', $fanId)
            ->where('creator_id', $creatorId)
            ->firstOrFail();

        $stripeSubscription = $session->subscription;

        $status = 'pending';

        if (
            $session->payment_status === 'paid' ||
            in_array(data_get($stripeSubscription, 'status'), ['active', 'trialing'])
        ) {
            $status = 'active';
        } elseif (in_array(data_get($stripeSubscription, 'status'), ['incomplete', 'past_due', 'unpaid'])) {
            $status = data_get($stripeSubscription, 'status');
        }

        $subscription->update([
            'stripe_checkout_session_id' => $session->id,
            'stripe_subscription_id' => is_object($stripeSubscription)
                ? $stripeSubscription->id
                : $session->subscription,
            'stripe_customer_id' => $session->customer,
            'stripe_account_destination' => data_get($stripeSubscription, 'transfer_data.destination')
                ?: $subscription->stripe_account_destination,
            'application_fee_percent' => data_get($stripeSubscription, 'application_fee_percent')
                ?: $subscription->application_fee_percent,
            'status' => $status,
            'subscribed_at' => $status === 'active' && !$subscription->subscribed_at
                ? now()
                : $subscription->subscribed_at,
        ]);

        $creator = User::findOrFail($creatorId);

        return view('subscriptions.success', [
            'creator' => $creator,
            'subscription' => $subscription->fresh(),
        ]);
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
