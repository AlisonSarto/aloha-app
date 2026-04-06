<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_commission_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_ledger_id')->nullable()
                  ->constrained('commission_ledger')->nullOnDelete();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('adjusted_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('original_value', 10, 2)->nullable();
            $table->decimal('adjusted_value', 10, 2);
            $table->decimal('delta', 10, 2);
            $table->text('reason');
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_commission_adjustments');
    }
};
