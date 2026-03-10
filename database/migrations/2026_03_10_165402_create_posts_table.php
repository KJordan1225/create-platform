<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->text('caption')->nullable();
            $table->boolean('is_locked')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
