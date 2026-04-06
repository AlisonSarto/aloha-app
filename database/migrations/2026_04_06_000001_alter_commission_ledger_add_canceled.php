<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_ledger', function (Blueprint $table) {
            $table->unsignedBigInteger('canceled_by')->nullable()->after('notes');
            $table->text('cancel_reason')->nullable()->after('canceled_by');
        });
    }

    public function down(): void
    {
        Schema::table('commission_ledger', function (Blueprint $table) {
            $table->dropColumn(['canceled_by', 'cancel_reason']);
        });
    }
};
