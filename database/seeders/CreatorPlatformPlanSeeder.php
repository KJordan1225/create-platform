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
                'name' => 'Creator Monthly',
                'price' => 1999,
                'currency' => 'usd',
                'interval' => 'month',
                'stripe_price_id' => env('STRIPE_CREATOR_MONTHLY_PRICE_ID'),
                'is_active' => true,
                'description' => 'Create and publish subscriber content on the platform.',
            ]
        );
    }
}
