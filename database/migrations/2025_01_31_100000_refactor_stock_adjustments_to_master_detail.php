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
        // Create stock_adjustment_details table first
        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->decimal('system_quantity', 15, 2)->default(0); // Jumlah di sistem
            $table->decimal('actual_quantity', 15, 2)->default(0); // Jumlah fisik/aktual
            $table->decimal('difference_quantity', 15, 2)->default(0); // Selisih (actual - system)
            $table->enum('adjustment_type', ['increase', 'decrease']); // Jenis per item
            $table->string('reason')->nullable(); // Alasan per item
            $table->text('notes')->nullable(); // Catatan per item
            $table->timestamps();

            // Indexes
            $table->index(['stock_adjustment_id', 'product_id']);
        });

        // Modify stock_adjustments table to be master only
        Schema::table('stock_adjustments', function (Blueprint $table) {
            // Drop product-specific columns
            $table->dropForeign(['product_id']);
            $table->dropColumn([
                'product_id',
                'system_quantity',
                'actual_quantity',
                'difference_quantity',
                'adjustment_type',
                'reason'
            ]);

            // Add master-level columns
            $table->integer('total_items')->default(0)->after('adjustment_date'); // Total product items
            $table->text('description')->nullable()->after('total_items'); // Deskripsi keseluruhan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore stock_adjustments original structure
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['total_items', 'description']);

            $table->foreignId('product_id')->after('adjustment_date')->constrained('products')->onDelete('restrict');
            $table->integer('system_quantity')->after('product_id');
            $table->integer('actual_quantity')->after('system_quantity');
            $table->integer('difference_quantity')->after('actual_quantity');
            $table->enum('adjustment_type', ['increase', 'decrease'])->after('difference_quantity');
            $table->string('reason')->nullable()->after('adjustment_type');
        });

        Schema::dropIfExists('stock_adjustment_details');
    }
};
