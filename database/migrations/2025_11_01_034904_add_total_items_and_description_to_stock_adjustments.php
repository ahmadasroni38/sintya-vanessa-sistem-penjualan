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
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('stock_adjustments', 'total_items')) {
                $table->integer('total_items')->default(0)->after('adjustment_date');
            }
            if (!Schema::hasColumn('stock_adjustments', 'description')) {
                $table->text('description')->nullable()->after('adjustment_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('stock_adjustments', 'total_items')) {
                $table->dropColumn('total_items');
            }
            if (Schema::hasColumn('stock_adjustments', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
