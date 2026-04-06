<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL does not support modifying enum columns directly; recreate via raw SQL.
        // SQLite (used in tests) stores ENUMs as VARCHAR — no modification needed.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE coupons MODIFY COLUMN discount_type ENUM('percent','fixed','shipping') NOT NULL");
        }

        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE coupons MODIFY COLUMN discount_type ENUM('percent','fixed') NOT NULL");
        }

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
