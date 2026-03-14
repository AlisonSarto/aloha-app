<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('gestao_click_id')->unique();
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->foreignId('price_table_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            $table->boolean('can_use_boleto')->default(false);
            $table->unsignedInteger('boleto_due_days')->default(3);
            $table->unsignedInteger('orders_count')->default(0);

            $table->timestamps();
        });

        Schema::create('client_store', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->unique(['client_id', 'store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_store');
        Schema::dropIfExists('stores');
    }
};
