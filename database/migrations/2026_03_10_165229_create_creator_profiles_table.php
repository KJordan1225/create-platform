<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('display_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();

            $table->string('avatar_path')->nullable();
            $table->string('banner_path')->nullable();

            $table->decimal('monthly_price', 8, 2)->default(9.99);

            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();

            $table->boolean('is_published')->default(true);
            $table->boolean('allow_tips')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_profiles');
    }
};
