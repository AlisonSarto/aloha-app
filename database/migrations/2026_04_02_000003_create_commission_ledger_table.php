<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->string('gestao_click_order_id')->nullable()->index();
            $table->date('order_date');
            $table->decimal('sale_value', 10, 2);
            $table->unsignedInteger('packages_count')->default(0);
            $table->enum('commission_type', ['new_store', 'recurring']);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_value', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['seller_id', 'gestao_click_order_id'], 'commission_unique_order');
            $table->index(['seller_id', 'order_date']);
            $table->index(['store_id', 'order_date']);
            $table->index(['seller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_ledger');
    }
};
