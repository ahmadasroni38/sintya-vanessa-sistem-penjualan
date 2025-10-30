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
        Schema::table('stock_in', function (Blueprint $table) {
            // Remove product-specific columns (moved to details)
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity', 'unit_price']);

            // total_price will now be calculated from sum of details
            // Keep supplier_name, reference_number, notes in header
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_in', function (Blueprint $table) {
            $table->foreignId('product_id')->after('location_id')->constrained('products')->onDelete('restrict');
            $table->decimal('quantity', 15, 2)->after('product_id');
            $table->decimal('unit_price', 15, 2)->after('quantity');
        });
    }
};
