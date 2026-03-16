<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('gross_subscription_revenue', 10, 2)->default(0);
            $table->decimal('gross_tip_revenue', 10, 2)->default(0);
            $table->decimal('gross_total', 10, 2)->default(0);

            $table->decimal('platform_fee_total', 10, 2)->default(0);
            $table->decimal('estimated_processor_fee_total', 10, 2)->default(0);
            $table->decimal('net_creator_amount', 10, 2)->default(0);

            $table->string('status')->default('pending'); // pending, approved, paid, failed
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['creator_id', 'period_start', 'period_end']);
            $table->index(['status', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_reports');
    }
};
