<?php

namespace App\Services;

use App\Models\CreatorProfile;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class StripeCreatorBillingService
{
    public function syncCreatorSubscriptionPrice(CreatorProfile $profile): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $productId = $profile->stripe_product_id;

        if (! $productId) {
            $product = Product::create([
                'name' => $profile->display_name . ' Subscription',
                'metadata' => [
                    'creator_profile_id' => $profile->id,
                    'user_id' => $profile->user_id,
                ],
            ]);

            $productId = $product->id;
        }

        $currentAmount = (int) round($profile->monthly_price * 100);
        $needsNewPrice = true;

        if ($profile->stripe_price_id) {
            try {
                $existingPrice = Price::retrieve($profile->stripe_price_id);
                $needsNewPrice = (int) $existingPrice->unit_amount !== $currentAmount;
            } catch (\Throwable $e) {
                $needsNewPrice = true;
            }
        }

        $priceId = $profile->stripe_price_id;

        if ($needsNewPrice) {
            $price = Price::create([
                'unit_amount' => $currentAmount,
                'currency' => strtolower(config('cashier.currency', 'usd')),
                'recurring' => [
                    'interval' => 'month',
                ],
                'product' => $productId,
                'metadata' => [
                    'creator_profile_id' => $profile->id,
                    'user_id' => $profile->user_id,
                ],
            ]);

            $priceId = $price->id;
        }

        $profile->update([
            'stripe_product_id' => $productId,
            'stripe_price_id' => $priceId,
        ]);
    }
}
