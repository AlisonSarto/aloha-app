<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('coupon_usages')) {
            return;
        }

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('gestao_click_order_id')->nullable()->comment('ID da venda no GestãoClick');
            $table->timestamp('used_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
