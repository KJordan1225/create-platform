<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fan_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('usd');

            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();

            $table->string('stripe_account_destination')->nullable();
            $table->unsignedBigInteger('application_fee_amount')->nullable(); // cents
            $table->decimal('application_fee_percent', 5, 2)->nullable();

            $table->string('status')->default('pending'); // pending, succeeded, failed, canceled
            $table->text('message')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['fan_id', 'creator_id']);
            $table->index('stripe_checkout_session_id');
            $table->index('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
