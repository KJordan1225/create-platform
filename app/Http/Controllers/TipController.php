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

        $data = $request->validated();

        Stripe::setApiKey(config('services.stripe.secret'));

        $amountInCents = (int) round($data['amount'] * 100);

        if ($abuseDetectionService->isTipAbuse($fan, (float) $data['amount'])) {
            return back()->withErrors([
                'amount' => 'This tip pattern looks unusual. Please wait and try again later.',
            ]);
        }

        $session = Session::create([
            'mode' => 'payment',
            'customer_email' => $fan->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => config('cashier.currency', 'usd'),
                    'product_data' => [
                        'name' => 'Tip for ' . $profile->display_name,
                    ],
                    'unit_amount' => $amountInCents,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('tips.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('creators.show', $profile->slug),
            'metadata' => [
                'fan_id' => $fan->id,
                'creator_id' => $creator->id,
                'message' => $data['message'] ?? '',
                'type' => 'creator_tip',
            ],
        ]);

        Tip::create([
            'fan_id' => $fan->id,
            'creator_id' => $creator->id,
            'amount' => $data['amount'],
            'currency' => config('cashier.currency', 'usd'),
            'message' => $data['message'] ?? null,
            'stripe_checkout_session_id' => $session->id,
            'status' => 'pending',
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        return redirect()
            ->route('dashboard')
            ->with('success', 'Your tip checkout was completed. Final status will update after payment confirmation.');
    }
}