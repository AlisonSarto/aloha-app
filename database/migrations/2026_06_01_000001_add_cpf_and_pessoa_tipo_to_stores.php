<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('cpf')->nullable()->unique()->after('cnpj');
            $table->enum('pessoa_tipo', ['PJ', 'PF'])->default('PJ')->after('cpf');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropColumn(['cpf', 'pessoa_tipo']);
        });
    }
};
