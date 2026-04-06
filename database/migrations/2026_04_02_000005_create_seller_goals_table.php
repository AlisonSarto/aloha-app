<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->smallInteger('year')->unsigned();
            $table->tinyInteger('month')->unsigned();
            $table->unsignedInteger('new_stores_target')->nullable();
            $table->unsignedInteger('active_stores_target')->nullable();
            $table->unsignedInteger('packages_target')->nullable();
            $table->boolean('new_stores_enabled')->default(false);
            $table->boolean('active_stores_enabled')->default(false);
            $table->boolean('packages_enabled')->default(false);
            $table->timestamps();

            $table->unique(['seller_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_goals');
    }
};
