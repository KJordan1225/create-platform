<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable()->after('email');

            $table->boolean('stripe_charges_enabled')->default(false)->after('stripe_account_id');
            $table->boolean('stripe_payouts_enabled')->default(false)->after('stripe_charges_enabled');

            $table->string('stripe_onboarding_status')->default('pending')->after('stripe_payouts_enabled');
            // connected | pending | needs_action

            $table->json('stripe_requirements')->nullable()->after('stripe_onboarding_status');
            $table->timestamp('stripe_onboarded_at')->nullable()->after('stripe_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_account_id',
                'stripe_charges_enabled',
                'stripe_payouts_enabled',
                'stripe_onboarding_status',
                'stripe_requirements',
                'stripe_onboarded_at',
            ]);
        });
    }
};
