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
        Schema::create('price_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('price_table_ranges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('price_table_id')
                    ->constrained()
                    ->cascadeOnDelete();

            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();

            $table->decimal('unit_price', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_tables');
        Schema::dropIfExists('price_table_range');
    }
};
