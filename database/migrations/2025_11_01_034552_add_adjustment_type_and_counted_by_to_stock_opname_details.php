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
        Schema::table('stock_opname_details', function (Blueprint $table) {
            $table->enum('adjustment_type', ['increase', 'decrease'])->nullable()->after('difference_quantity');
            $table->foreignId('counted_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_opname_details', function (Blueprint $table) {
            $table->dropForeign(['counted_by']);
            $table->dropColumn(['adjustment_type', 'counted_by']);
        });
    }
};
