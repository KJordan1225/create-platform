<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_platform_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_platform_plan_id')->constrained()->cascadeOnDelete();

            $table->string('provider')->default('stripe');
            $table->string('stripe_checkout_session_id')->nullable()->index();
            $table->string('stripe_subscription_id')->nullable()->index();
            $table->string('stripe_customer_id')->nullable()->index();

            $table->string('status')->default('inactive')->index();
            // inactive, trialing, active, past_due, canceled, unpaid

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_platform_subscriptions');
    }
};
