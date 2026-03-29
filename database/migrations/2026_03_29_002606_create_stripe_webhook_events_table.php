<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('stripe');
            $table->string('event_id')->nullable()->index();
            $table->string('event_type')->nullable()->index();
            $table->unsignedSmallInteger('http_status')->default(200);
            $table->boolean('processed')->default(false)->index();
            $table->text('message')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_events');
    }
};
