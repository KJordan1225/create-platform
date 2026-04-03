<?php

namespace Database\Seeders;

use App\Models\CreatorPlatformPlan;
use Illuminate\Database\Seeder;

class CreatorPlatformPlanSeeder extends Seeder
{
    public function run(): void
    {
        CreatorPlatformPlan::updateOrCreate(
            ['slug' => 'creator-monthly'],
            [
                'name' => 'Creator One-time',
                'price' => 1000,
                'currency' => 'usd',
                'interval' => 'one-tme',
                'stripe_price_id' => env('STRIPE_CREATOR_MONTHLY_PRICE_ID'),
                'is_active' => true,
                'has_trial' => true,
                'trial_days' => 7,
                'description' => 'Unlock post publishing, creator tools, and platform visibility.',
                'features' => [
                    'Create and publish posts',
                    'Manage subscriber content',
                    'Access creator dashboard tools',
                    'Optional free trial support',
                ],
            ]
        );
    }
}
