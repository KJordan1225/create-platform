<?php

namespace App\Http\Controllers;

use App\Mail\NewSubscriberMail;
use App\Models\Plf_subscription;
use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
        }

        return response('Webhook handled', 200);
    }

    protected function handleCheckoutSessionCompleted(object $session): void
    {
        $metadata = (array) ($session->metadata ?? []);
        $type = $metadata['type'] ?? null;

        if ($type === 'creator_subscription') {
            $subscription = Plf_subscription::query()
                ->where('fan_id', $metadata['fan_id'] ?? null)
                ->where('creator_id', $metadata['creator_id'] ?? null)
                ->first();

            if ($subscription) {
                $subscription->update([
                    'stripe_subscription_id' => $session->subscription ?? null,
                    'stripe_checkout_session_id' => $session->id ?? null,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                    'canceled_at' => null,
                ]);

                $subscription->load(['creator', 'fan']);

                if ($subscription->creator?->email) {
                    Mail::to($subscription->creator->email)->queue(new NewSubscriberMail($subscription));
                }
            }
        }

        if ($type === 'creator_tip') {
            Tip::query()
                ->where('stripe_checkout_session_id', $session->id ?? null)
                ->update([
                    'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    'status' => 'succeeded',
                ]);
        }
    }

    protected function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        Plf_subscription::query()
            ->where('stripe_subscription_id', $stripeSubscription->id ?? null)
            ->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'ends_at' => now(),
            ]);
    }

    protected function handleInvoicePaymentFailed(object $invoice): void
    {
        $subscriptionId = $invoice->subscription ?? null;

        if (! $subscriptionId) {
            return;
        }

        Plf_subscription::query()
            ->where('stripe_subscription_id', $subscriptionId)
            ->update([
                'status' => 'past_due',
            ]);
    }
}
