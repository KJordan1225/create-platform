<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('role')->default('fan')->after('password'); // admin, creator, fan
            $table->boolean('is_active')->default(true)->after('role');
            $table->boolean('is_creator')->default(false)->after('is_active');
            $table->timestamp('creator_approved_at')->nullable()->after('is_creator');
            $table->timestamp('last_seen_at')->nullable()->after('creator_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'role',
                'is_active',
                'is_creator',
                'creator_approved_at',
                'last_seen_at',
            ]);
        });
    }
};
