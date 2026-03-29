<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\CreatorPlatformPlan;
use App\Models\CreatorPlatformSubscription;
use App\Models\StripeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
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

        $log = StripeWebhookEvent::create([
            'provider' => 'stripe',
            'headers' => $request->headers->all(),
            'payload' => json_decode($payload, true),
            'received_at' => now(),
            'processed' => false,
            'http_status' => 200,
        ]);

        try {
            if ($secret) {
                $event = Webhook::constructEvent($payload, $signature, $secret);
            } else {
                $event = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
            }

            $log->update([
                'event_id' => $event->id ?? null,
                'event_type' => $event->type ?? null,
            ]);
        } catch (UnexpectedValueException|SignatureVerificationException|\JsonException $e) {
            $log->update([
                'http_status' => 400,
                'processed' => false,
                'message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return response('Invalid webhook payload.', 400);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
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

            $log->update([
                'processed' => true,
                'message' => 'Webhook handled successfully.',
                'processed_at' => now(),
            ]);

            return response('Webhook handled.', 200);
        } catch (\Throwable $e) {
            $log->update([
                'http_status' => 500,
                'processed' => false,
                'message' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            return response('Webhook failed.', 500);
        }
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
                'is_trial' => ($stripeSubscription->status ?? null) === 'trialing',
                'trial_ends_at' => isset($stripeSubscription->trial_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                    : null,
                'starts_at' => now(),
                'renews_at' => isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'ends_at' => !empty($stripeSubscription->cancel_at_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
                'canceled_at' => !empty($stripeSubscription->canceled_at)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                    : null,
                'meta' => [
                    'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                    'last_webhook' => 'checkout.session.completed',
                ],
            ]
        );
    }

    protected function handleSubscriptionUpdated(object $stripeSubscription): void
    {
        $subscription = CreatorPlatformSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => $stripeSubscription->status ?? $subscription->status,
            'is_trial' => ($stripeSubscription->status ?? null) === 'trialing',
            'trial_ends_at' => isset($stripeSubscription->trial_end)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'renews_at' => isset($stripeSubscription->current_period_end)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : $subscription->renews_at,
            'ends_at' => !empty($stripeSubscription->cancel_at_period_end)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null,
            'canceled_at' => !empty($stripeSubscription->canceled_at)
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                : null,
            'meta' => array_merge($subscription->meta ?? [], [
                'cancel_at_period_end' => (bool) ($stripeSubscription->cancel_at_period_end ?? false),
                'last_webhook' => 'customer.subscription.updated',
            ]),
        ]);

        \App\Models\CreatorSubscriptionAudit::create([
            'creator_platform_subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'action' => 'webhook_updated',
            'note' => 'Subscription updated from Stripe webhook.',
            'new_values' => $subscription->fresh()->only([
                'status',
                'is_trial',
                'trial_ends_at',
                'renews_at',
                'ends_at',
                'canceled_at',
            ]),
            'meta' => [
                'webhook' => 'customer.subscription.updated',
                'stripe_subscription_id' => $subscription->stripe_subscription_id,
            ],
        ]);
    }

    protected function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $subscription = CreatorPlatformSubscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
            'ends_at' => now(),
            'canceled_at' => now(),
            'meta' => array_merge($subscription->meta ?? [], [
                'cancel_at_period_end' => false,
                'last_webhook' => 'customer.subscription.deleted',
            ]),
        ]);

        \App\Models\CreatorSubscriptionAudit::create([
            'creator_platform_subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'action' => 'webhook_updated',
            'note' => 'Subscription deleted from Stripe webhook.',
            'new_values' => $subscription->fresh()->only([
                'status',
                'ends_at',
                'canceled_at',
            ]),
            'meta' => [
                'webhook' => 'customer.subscription.deleted',
                'stripe_subscription_id' => $subscription->stripe_subscription_id,
            ],
        ]);
    }
}
