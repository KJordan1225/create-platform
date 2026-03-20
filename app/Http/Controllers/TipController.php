<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutTipRequest;
use App\Models\Tip;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Services\AbuseDetectionService;

class TipController extends Controller
{
    public function showCheckout(User $creator)
    {
        abort_unless($creator->isApprovedCreator(), 404);

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

        if (! $profile->allow_tips) {
            abort(404);
        }

        return view('tips.checkout', compact('creator', 'profile'));
    }

    public function checkout(CheckoutTipRequest $request, User $creator, AbuseDetectionService $abuseDetectionService)
    {
        abort_unless($creator->isApprovedCreator(), 404);

        $fan = $request->user();

        abort_if($fan->id === $creator->id, 403, 'You cannot tip yourself.');

        $profile = $creator->creatorProfile;

        if (! $profile->allow_tips) {
            abort(404);
        }

       if (empty($creator->stripe_account_id)) {
            return back()->withErrors([
                'tip' => 'This creator is not connected for payouts yet.',
            ]);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = number_format((float) $validated['amount'], 2, '.', '');
        $amountCents = (int) round($amount * 100);
        $platformFeeCents = (int) round($amountCents * 0.20);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'customer_email' => $fan->email,

            'line_items' => [[
                'price_data' => [
                    'currency' => config('cashier.currency', 'usd'),
                    'product_data' => [
                        'name' => 'Tip for @' . $creator->username,
                    ],
                    'unit_amount' => $amountCents,
                ],
                'quantity' => 1,
            ]],

            'payment_intent_data' => [
                'application_fee_amount' => $platformFeeCents,
                'transfer_data' => [
                    'destination' => $creator->stripe_account_id,
                ],
                'metadata' => [
                    'fan_id' => (string) $fan->id,
                    'creator_id' => (string) $creator->id,
                    'type' => 'creator_tip',
                ],
            ],

            'metadata' => [
                'fan_id' => (string) $fan->id,
                'creator_id' => (string) $creator->id,
                'type' => 'creator_tip',
                'tip_message' => $validated['message'] ?? '',
            ],

            'success_url' => route('tips.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creators.show', $creator->creatorProfile->slug),
        ]);

        Tip::create([
            'fan_id' => $fan->id,
            'creator_id' => $creator->id,
            'amount' => $amount,
            'currency' => config('cashier.currency', 'usd'),
            'stripe_checkout_session_id' => $session->id,
            'stripe_account_destination' => $creator->stripe_account_id,
            'application_fee_amount' => $platformFeeCents,
            'application_fee_percent' => 20.00,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        abort_unless($sessionId, 404);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve([
            'id' => $sessionId,
            'expand' => ['payment_intent.latest_charge'],
        ]);

        $tip = Tip::where('stripe_checkout_session_id', $session->id)->firstOrFail();

        $paymentIntent = $session->payment_intent;

        $tip->update([
            'stripe_payment_intent_id' => is_object($paymentIntent) ? $paymentIntent->id : $session->payment_intent,
            'stripe_charge_id' => data_get($paymentIntent, 'latest_charge.id'),
            'status' => $session->payment_status === 'paid' ? 'succeeded' : $tip->status,
            'paid_at' => $session->payment_status === 'paid' && !$tip->paid_at ? now() : $tip->paid_at,
        ]);

        return view('tips.success', [
            'tip' => $tip->fresh(),
            'creator' => $tip->creator,
        ]);
    }
}