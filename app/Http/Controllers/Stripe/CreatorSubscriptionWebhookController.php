<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;
use UnexpectedValueException;

class CreatorSubscriptionWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            if ($secret) {
                $event = Webhook::constructEvent($payload, $signature, $secret);
            } else {
                $event = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
            }
        } catch (UnexpectedValueException|SignatureVerificationException|\JsonException $e) {
            return response('Invalid webhook payload.', 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $stripe = new StripeClient(config('services.stripe.secret'));

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object, $stripe);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
        }

        return response('Webhook handled.', 200);
    }

    protected function handleCheckoutSessionCompleted(object $session, StripeClient $stripe): void
    {
        if (($session->metadata->type ?? null) !== 'creator_platform_subscription') {
            return;
        }

        $userId = (int) ($session->metadata->user_id ?? 0);
        $planId = (int) ($session->metadata->plan_id ?? 0);

        if (!$userId || !$planId || empty($session->subscription)) {
            return;
        }

        $plan = CreatorPlatformPlan::find($planId);

        if (!$plan) {
            return;
        }

        $stripeSubscription = $stripe->subscriptions->retrieve($session->subscription, []);

        CreatorPlatformSubscription::updateOrCreate(
            [
                'stripe_subscription_id' => $session->subscription,
            ],
            [
                'user_id' => $userId,
                'creator_platform_plan_id' => $plan->id,
                'provider' => 'stripe',
                'stripe_checkout_session_id' => $session->id ?? null,
                'stripe_customer_id' => $session->customer ?? null,
                'status' => $stripeSubscription->status ?? 'active',
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
                    'checkout_session_id' => $session->id ?? null,
                    'webhook' => 'checkout.session.completed',
                    'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                ],
            ]
        );
    }

    protected function handleSubscriptionUpdated(object $stripeSubscription): void
    {
        $subscription = CreatorPlatformSubscription::query()
            ->where('stripe_subscription_id', $stripeSubscription->id)
            ->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => $stripeSubscription->status ?? $subscription->status,
            'renews_at' => isset($stripeSubscription->current_period_end)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : $subscription->renews_at,
            'ends_at' => !empty($stripeSubscription->cancel_at_period_end)
                && isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
            'canceled_at' => !empty($stripeSubscription->canceled_at)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                : null,
            'meta' => array_merge($subscription->meta ?? [], [
                'last_webhook' => 'customer.subscription.updated',
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
            ]),
        ]);
    }

    protected function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $subscription = CreatorPlatformSubscription::query()
            ->where('stripe_subscription_id', $stripeSubscription->id)
            ->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'ends_at' => now(),
            'canceled_at' => now(),
            'meta' => array_merge($subscription->meta ?? [], [
                'last_webhook' => 'customer.subscription.deleted',
                'cancel_at_period_end' => false,
            ]),
        ]);
    }
}