<?php

namespace App\Http\Controllers;

use App\Models\CreatorPlatformSubscription;
use App\Models\CreatorSubscriptionWebhookLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->server('HTTP_STRIPE_SIGNATURE');
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            Log::error('Stripe webhook secret missing.');
            return response('Webhook secret not configured.', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload.', [
                'message' => $e->getMessage(),
            ]);

            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed.', [
                'message' => $e->getMessage(),
            ]);

            return response('Invalid signature.', 400);
        }

        $this->storeWebhookLog($event, $payload);

        match ($event->type) {
            'checkout.session.completed'      => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.updated'  => $this->handleCustomerSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted'  => $this->handleCustomerSubscriptionDeleted($event->data->object),
            default                          => null,
        };

        return response('Webhook handled.', 200);
    }

    protected function handleCheckoutSessionCompleted(object $session): void
    {
        if (($session->mode ?? null) !== 'subscription') {
            return;
        }

        $metadata = (array) ($session->metadata ?? []);

        if (($metadata['type'] ?? null) !== 'creator_platform_subscription') {
            return;
        }

        $userId = $metadata['user_id'] ?? null;
        $planId = $metadata['plan_id'] ?? null;
        $stripeSubscriptionId = $session->subscription ?? null;
        $stripeCustomerId = $session->customer ?? null;

        if (!$userId || !$planId || !$stripeSubscriptionId) {
            Log::warning('Stripe checkout.session.completed missing required metadata.', [
                'session_id' => $session->id ?? null,
                'metadata' => $metadata,
            ]);
            return;
        }

        $subscription = CreatorPlatformSubscription::firstOrNew([
            'user_id' => $userId,
        ]);

        $subscription->fill([
            'plan_id' => $planId,
            'stripe_checkout_session_id' => $session->id ?? null,
            'stripe_customer_id' => $stripeCustomerId,
            'stripe_subscription_id' => $stripeSubscriptionId,
            'status' => 'active',
            'started_at' => now(),
            'renews_at' => null,
            'ends_at' => null,
            'canceled_at' => null,
            'raw_payload' => $this->safeJson($session),
        ]);

        $subscription->save();
    }

    protected function handleCustomerSubscriptionUpdated(object $stripeSubscription): void
    {
        $local = CreatorPlatformSubscription::where(
            'stripe_subscription_id',
            $stripeSubscription->id
        )->first();

        if (!$local) {
            Log::warning('Local creator subscription not found for Stripe subscription update.', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        $status = $this->mapStripeStatusToLocalStatus($stripeSubscription->status ?? null);

        $periodStart = !empty($stripeSubscription->current_period_start)
            ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
            : null;

        $periodEnd = !empty($stripeSubscription->current_period_end)
            ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
            : null;

        $cancelAt = !empty($stripeSubscription->cancel_at)
            ? Carbon::createFromTimestamp($stripeSubscription->cancel_at)
            : null;

        $canceledAt = !empty($stripeSubscription->canceled_at)
            ? Carbon::createFromTimestamp($stripeSubscription->canceled_at)
            : null;

        $cancelAtPeriodEnd = (bool) ($stripeSubscription->cancel_at_period_end ?? false);

        $local->status = $status;
        $local->started_at = $local->started_at ?: $periodStart;
        $local->renews_at = $status === 'active' && !$cancelAtPeriodEnd ? $periodEnd : null;
        $local->ends_at = $cancelAtPeriodEnd ? ($cancelAt ?: $periodEnd) : null;
        $local->canceled_at = $canceledAt;
        $local->raw_payload = $this->safeJson($stripeSubscription);

        $local->save();
    }

    protected function handleCustomerSubscriptionDeleted(object $stripeSubscription): void
    {
        $local = CreatorPlatformSubscription::where(
            'stripe_subscription_id',
            $stripeSubscription->id
        )->first();

        if (!$local) {
            Log::warning('Local creator subscription not found for Stripe subscription delete.', [
                'stripe_subscription_id' => $stripeSubscription->id,
            ]);
            return;
        }

        $local->status = 'canceled';
        $local->canceled_at = now();
        $local->ends_at = now();
        $local->renews_at = null;
        $local->raw_payload = $this->safeJson($stripeSubscription);

        $local->save();
    }

    protected function mapStripeStatusToLocalStatus(?string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'trialing' => 'trialing',
            'active' => 'active',
            'past_due' => 'past_due',
            'unpaid' => 'unpaid',
            'canceled' => 'canceled',
            'incomplete' => 'incomplete',
            'incomplete_expired' => 'expired',
            default => 'inactive',
        };
    }

    protected function safeJson(object $payload): array
    {
        return json_decode(json_encode($payload), true) ?? [];
    }

    protected function storeWebhookLog(object $event, string $payload): void
    {
        if (!class_exists(CreatorSubscriptionWebhookLog::class)) {
            return;
        }

        CreatorSubscriptionWebhookLog::updateOrCreate(
            ['stripe_event_id' => $event->id],
            [
                'event_type' => $event->type,
                'payload' => json_decode($payload, true),
                'processed_at' => now(),
            ]
        );
    }
}