<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('usage_limit')->nullable()->comment('Limite de usos por store; null = sem limite');
            $table->timestamps();

            $table->unique(['coupon_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_store');
    }
};
