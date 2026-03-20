<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plf_subscriptions', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('stripe_subscription_id');
            $table->string('stripe_account_destination')->nullable()->after('stripe_customer_id');
            $table->decimal('application_fee_percent', 5, 2)->nullable()->after('stripe_account_destination');
            $table->timestamp('subscribed_at')->nullable()->after('application_fee_percent');
        });
    }

    public function down(): void
    {
        Schema::table('plf_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_account_destination',
                'application_fee_percent',
                'subscribed_at',
            ]);
        });
    }
};
