<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_platform_subscriptions', function (Blueprint $table) {
            $table->boolean('is_trial')->default(false)->after('status');
            $table->timestamp('trial_ends_at')->nullable()->after('is_trial');
            $table->timestamp('revoked_at')->nullable()->after('canceled_at');
            $table->foreignId('assigned_by')->nullable()->after('revoked_at')->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable()->after('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::table('creator_platform_subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_by');
            $table->dropColumn([
                'is_trial',
                'trial_ends_at',
                'revoked_at',
                'admin_note',
            ]);
        });
    }
};
