<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_subscription_audits', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('creator_platform_subscription_id')->nullable();

            $table->foreign(
            'creator_platform_subscription_id',
            'cs_audit_cps_id_fk' // 👈 short, custom name
            )->references('id')
            ->on('creator_platform_subscriptions')
            ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('admin_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 100)->index();
            // assigned, revoked, expired, trial_expired, cancel_scheduled, reactivated, canceled_now, webhook_updated

            $table->text('note')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_subscription_audits');
    }
};
