<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_platform_subscriptions', function (Blueprint $table) {
            $table->timestamp('trial_ending_notice_sent_at')->nullable()->after('admin_note');
            $table->timestamp('cancel_scheduled_notice_sent_at')->nullable()->after('trial_ending_notice_sent_at');
            $table->timestamp('revoked_notice_sent_at')->nullable()->after('cancel_scheduled_notice_sent_at');
            $table->timestamp('expired_at')->nullable()->after('revoked_notice_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('creator_platform_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'trial_ending_notice_sent_at',
                'cancel_scheduled_notice_sent_at',
                'revoked_notice_sent_at',
                'expired_at',
            ]);
        });
    }
};
