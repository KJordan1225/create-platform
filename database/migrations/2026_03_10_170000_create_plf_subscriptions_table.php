<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plf_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();

            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_checkout_session_id')->nullable()->unique();

            $table->decimal('amount', 8, 2);
            $table->string('currency', 10)->default('usd');

            $table->string('status')->default('pending'); 
            // pending, active, canceled, incomplete, past_due, unpaid

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->timestamps();

            $table->unique(['fan_id', 'creator_id']);
            $table->index(['creator_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plf_subscriptions');
    }
};