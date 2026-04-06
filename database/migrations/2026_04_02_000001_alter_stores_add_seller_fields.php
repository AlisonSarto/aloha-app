<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // These columns already exist in the production database from a prior untracked migration.
        // We add them conditionally so the migrations run cleanly in test environments (SQLite).
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->constrained('sellers')->nullOnDelete();
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_status')) {
                $table->string('seller_assignment_status', 20)->default('unassigned');
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_requested_by')) {
                $table->foreignId('seller_assignment_requested_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_requested_at')) {
                $table->timestamp('seller_assignment_requested_at')->nullable();
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_approved_by')) {
                $table->foreignId('seller_assignment_approved_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_approved_at')) {
                $table->timestamp('seller_assignment_approved_at')->nullable();
            }
            if (!Schema::hasColumn('stores', 'seller_assignment_reason')) {
                $table->text('seller_assignment_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            foreach ([
                'seller_assignment_reason',
                'seller_assignment_approved_at',
                'seller_assignment_approved_by',
                'seller_assignment_requested_at',
                'seller_assignment_requested_by',
                'seller_assignment_status',
                'seller_id',
            ] as $col) {
                if (Schema::hasColumn('stores', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
