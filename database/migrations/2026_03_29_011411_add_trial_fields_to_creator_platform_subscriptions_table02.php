<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creator_platform_plans', function (Blueprint $table) {
            $table->string('features')->after('has_trial');
        });
    }

    public function down(): void
    {
        Schema::table('creator_platform_plans', function (Blueprint $table) {
            $table->dropColumn([
                'features',
            ]);
        });
    }
};
