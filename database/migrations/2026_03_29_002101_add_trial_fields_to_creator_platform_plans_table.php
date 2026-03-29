<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_platform_plans', function (Blueprint $table) {
            $table->boolean('has_trial')->default(false)->after('is_active');
            $table->unsignedInteger('trial_days')->default(0)->after('has_trial');
        });
    }

    public function down(): void
    {
        Schema::table('creator_platform_plans', function (Blueprint $table) {
            $table->dropColumn([
                'has_trial',
                'trial_days',
            ]);
        });
    }
};
