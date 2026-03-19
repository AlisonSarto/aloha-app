<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_configs', function (Blueprint $table) {
            $table->id();
            $table->json('delivery_days'); // 0=Mon ... 6=Sun; default set by model
            $table->unsignedTinyInteger('lead_days')->default(1);
            $table->enum('late_direction', ['before', 'after'])->default('after');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_configs');
    }
};
