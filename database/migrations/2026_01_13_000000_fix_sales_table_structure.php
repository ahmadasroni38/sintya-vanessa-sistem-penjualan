<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists and has wrong structure (sale_number instead of transaction_number)
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'sale_number')) {
            // Drop the incorrect sales table
            Schema::dropIfExists('sale_details');
            Schema::dropIfExists('sales');
        }

        // Recreate sales table with correct structure
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_number', 20)->unique();
                $table->date('transaction_date');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('location_id');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('change_amount', 15, 2)->default(0);
                $table->enum('payment_method', ['cash', 'transfer', 'credit'])->default('cash');
                $table->text('notes')->nullable();
                $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('posted_by')->nullable();
                $table->timestamp('posted_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
                $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');

                $table->index(['status', 'transaction_date']);
                $table->index(['customer_id', 'status']);
                $table->index('transaction_number');
            });
        }

        // Recreate sale_details table with correct structure
        if (!Schema::hasTable('sale_details')) {
            Schema::create('sale_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sale_id');
                $table->unsignedBigInteger('product_id');
                $table->decimal('quantity', 10, 2);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percent', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_price', 15, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

                $table->index('sale_id');
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_details');
        Schema::dropIfExists('sales');
    }
};
