<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class StripeConnectController extends Controller
{
    public function settings(Request $request)
    {
        $creator = $request->user();

        abort_unless($creator && $creator->is_creator, 403);

        return view('creator.settings.payouts', compact('creator'));
    }

    public function connect(Request $request)
    {
        $creator = $request->user();

        abort_unless($creator && $creator->is_creator, 403);

        Stripe::setApiKey(config('services.stripe.secret'));

        if (!$creator->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'email' => $creator->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'metadata' => [
                    'user_id' => (string) $creator->id,
                    'type' => 'creator',
                ],
            ]);

            $creator->update([
                'stripe_account_id' => $account->id,
                'stripe_onboarding_status' => 'pending',
            ]);
        }

        $accountLink = AccountLink::create([
            'account' => $creator->stripe_account_id,
            'refresh_url' => route('creator.stripe.refresh'),
            'return_url' => route('creator.stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    public function refresh(Request $request)
    {
        return redirect()->route('creator.stripe.connect');
    }

    public function handleReturn(Request $request)
    {
        $creator = $request->user();

        abort_unless($creator && $creator->is_creator, 403);

        if (!$creator->stripe_account_id) {
            return redirect()
                ->route('creator.settings.payouts')
                ->with('error', 'Stripe account not found.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Account::retrieve($creator->stripe_account_id);

        $requirements = $account->requirements ?? null;

        $currentlyDue = $requirements->currently_due ?? [];
        $eventuallyDue = $requirements->eventually_due ?? [];
        $pastDue = $requirements->past_due ?? [];
        $pendingVerification = $requirements->pending_verification ?? [];

        $status = $this->determineOnboardingStatus(
            (bool) ($account->charges_enabled ?? false),
            (bool) ($account->payouts_enabled ?? false),
            $currentlyDue,
            $pastDue
        );

        $creator->update([
            'stripe_charges_enabled' => (bool) ($account->charges_enabled ?? false),
            'stripe_payouts_enabled' => (bool) ($account->payouts_enabled ?? false),
            'stripe_onboarding_status' => $status,
            'stripe_requirements' => [
                'currently_due' => $currentlyDue,
                'eventually_due' => $eventuallyDue,
                'past_due' => $pastDue,
                'pending_verification' => $pendingVerification,
                'disabled_reason' => $requirements->disabled_reason ?? null,
            ],
            'stripe_onboarded_at' => (
                (bool) ($account->charges_enabled ?? false)
                && (bool) ($account->payouts_enabled ?? false)
            ) ? ($creator->stripe_onboarded_at ?: now()) : $creator->stripe_onboarded_at,
        ]);

        return redirect()
            ->route('creator.settings.payouts')
            ->with(
                $status === 'connected' ? 'success' : ($status === 'needs_action' ? 'warning' : 'info'),
                $status === 'connected'
                    ? 'Your Stripe payout account is fully connected.'
                    : ($status === 'needs_action'
                        ? 'Stripe needs more information before payouts can be enabled.'
                        : 'Your Stripe onboarding has started but is not complete yet.')
            );
    }

    protected function determineOnboardingStatus(
        bool $chargesEnabled,
        bool $payoutsEnabled,
        array $currentlyDue,
        array $pastDue
    ): string {
        if ($chargesEnabled && $payoutsEnabled) {
            return 'connected';
        }

        if (!empty($currentlyDue) || !empty($pastDue)) {
            return 'needs_action';
        }

        return 'pending';
    }
}
