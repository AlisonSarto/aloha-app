<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE commission_ledger MODIFY COLUMN commission_type ENUM('new_store', 'recurring', 'manual') NOT NULL");
            DB::statement("ALTER TABLE commission_ledger MODIFY COLUMN status ENUM('pending', 'confirmed', 'paid', 'canceled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE commission_ledger MODIFY COLUMN commission_type ENUM('new_store', 'recurring') NOT NULL");
            DB::statement("ALTER TABLE commission_ledger MODIFY COLUMN status ENUM('pending', 'confirmed', 'paid') NOT NULL DEFAULT 'pending'");
        }
    }
};
