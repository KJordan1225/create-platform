<?php

namespace App\Http\Controllers;

use App\Models\Plf_subscription;
use App\Models\Tip;
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

    protected function handleCheckoutSessionCompleted($session): void
    {
        $type = data_get($session, 'metadata.type');

        if ($type === 'creator_subscription') {
            $fanId = data_get($session, 'metadata.fan_id');
            $creatorId = data_get($session, 'metadata.creator_id');

            $subscription = Plf_subscription::where('fan_id', $fanId)
                ->where('creator_id', $creatorId)
                ->first();

            if (!$subscription) {
                Log::warning('Subscription not found', ['session_id' => $session->id ?? null]);
                return;
            }

            $subscription->update([
                'stripe_checkout_session_id' => $session->id,
                'stripe_subscription_id' => $session->subscription ?? $subscription->stripe_subscription_id,
                'stripe_customer_id' => $session->customer ?? $subscription->stripe_customer_id,
                'status' => ($session->payment_status ?? null) === 'paid' ? 'active' : $subscription->status,
                'subscribed_at' => ($session->payment_status ?? null) === 'paid' && !$subscription->subscribed_at
                    ? now()
                    : $subscription->subscribed_at,
            ]);

            return;
        }

        if ($type === 'creator_tip') {
            $tip = Tip::where('stripe_checkout_session_id', $session->id)->first();

            if (!$tip) {
                Log::warning('Tip not found', ['session_id' => $session->id ?? null]);
                return;
            }

            $tip->update([
                'stripe_payment_intent_id' => $session->payment_intent ?? $tip->stripe_payment_intent_id,
                'status' => ($session->payment_status ?? null) === 'paid' ? 'succeeded' : $tip->status,
                'paid_at' => ($session->payment_status ?? null) === 'paid' && !$tip->paid_at
                    ? now()
                    : $tip->paid_at,
            ]);
        }
    }

    protected function handleCustomerSubscriptionUpdated($stripeSubscription): void
    {
        $fanId = data_get($stripeSubscription, 'metadata.fan_id');
        $creatorId = data_get($stripeSubscription, 'metadata.creator_id');

        $query = Plf_subscription::query();

        if ($fanId && $creatorId) {
            $query->where('fan_id', $fanId)
                  ->where('creator_id', $creatorId);
        } else {
            $query->where('stripe_subscription_id', $stripeSubscription->id);
        }

        $subscription = $query->first();

        if (!$subscription) {
            Log::warning('Local subscription not found', [
                'stripe_subscription_id' => $stripeSubscription->id ?? null,
            ]);
            return;
        }

        $status = $stripeSubscription->status ?? 'pending';

        $subscription->update([
            'stripe_subscription_id' => $stripeSubscription->id,
            'stripe_customer_id' => $stripeSubscription->customer ?? $subscription->stripe_customer_id,
            'stripe_account_destination' => data_get($stripeSubscription, 'transfer_data.destination')
                ?: $subscription->stripe_account_destination,
            'application_fee_percent' => data_get($stripeSubscription, 'application_fee_percent')
                ?: $subscription->application_fee_percent,
            'status' => $status,
            'subscribed_at' => in_array($status, ['active', 'trialing']) && !$subscription->subscribed_at
                ? now()
                : $subscription->subscribed_at,
            'canceled_at' => in_array($status, ['canceled', 'unpaid']) && !$subscription->canceled_at
                ? now()
                : $subscription->canceled_at,
        ]);
    }

    protected function handleCustomerSubscriptionDeleted($stripeSubscription): void
    {
        $subscription = Plf_subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Local subscription not found for delete', [
                'stripe_subscription_id' => $stripeSubscription->id ?? null,
            ]);
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }
}
