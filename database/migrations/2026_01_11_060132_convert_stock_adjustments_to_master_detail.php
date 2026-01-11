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
        // Create stock_adjustment_details table
        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('system_quantity'); // Jumlah di sistem
            $table->integer('actual_quantity'); // Jumlah fisik/aktual
            $table->integer('difference_quantity'); // Selisih (actual - system)
            $table->enum('adjustment_type', ['increase', 'decrease']); // Jenis adjustment
            $table->string('reason')->nullable(); // Alasan adjustment
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();
            $table->softDeletes();
        });

        // Remove product-related columns from stock_adjustments table
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Drop foreign key first if exists
            if (Schema::hasColumn('stock_adjustments', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn([
                    'product_id',
                    'system_quantity',
                    'actual_quantity',
                    'difference_quantity',
                    'adjustment_type',
                    'reason',
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore columns to stock_adjustments
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('restrict');
            $table->integer('system_quantity')->nullable();
            $table->integer('actual_quantity')->nullable();
            $table->integer('difference_quantity')->nullable();
            $table->enum('adjustment_type', ['increase', 'decrease'])->nullable();
            $table->string('reason')->nullable();
        });

        // Drop stock_adjustment_details table
        Schema::dropIfExists('stock_adjustment_details');
    }
};
