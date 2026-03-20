<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature.', 400);
        }

        switch ($event->type) {
            case 'account.updated':
                $this->handleAccountUpdated($event->data->object);
                break;

            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleCustomerSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleCustomerSubscriptionDeleted($event->data->object);
                break;
        }

        return response('Webhook handled', 200);
    }

    protected function handleAccountUpdated($account): void
    {
        $stripeAccountId = $account->id ?? null;

        if (!$stripeAccountId) {
            return;
        }

        $creator = User::where('stripe_account_id', $stripeAccountId)->first();

        if (!$creator) {
            Log::warning('Stripe account.updated received for unknown account', [
                'stripe_account_id' => $stripeAccountId,
            ]);
            return;
        }

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

    protected function handleCheckoutSessionCompleted($session): void
    {
        // keep your existing subscription/tip webhook logic here
    }

    protected function handleCustomerSubscriptionUpdated($stripeSubscription): void
    {
        // keep your existing subscription webhook logic here
    }

    protected function handleCustomerSubscriptionDeleted($stripeSubscription): void
    {
        // keep your existing subscription webhook logic here
    }
}
