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

            $table->decimal('amount', 8, 2);
            $table->string('currency', 10)->default('usd');

            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_checkout_session_id')->nullable()->unique();

            $table->string('status')->default('pending'); // pending, succeeded, failed, refunded
            $table->text('message')->nullable();

            $table->timestamps();

            $table->index(['creator_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
