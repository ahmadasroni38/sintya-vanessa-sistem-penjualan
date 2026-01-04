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
        // Create stock_balances table if not exists
        if (!Schema::hasTable('stock_balances')) {
            Schema::create('stock_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('location_id');
                $table->decimal('current_balance', 10, 2)->default(0.00);
                $table->decimal('minimum_stock', 10, 2)->nullable();
                $table->decimal('maximum_stock', 10, 2)->nullable();
                $table->string('status')->default('in_stock');
                $table->date('last_transaction_date')->nullable();
                $table->string('last_transaction_type')->nullable();
                $table->timestamps();

                // Indexes
                $table->index(['product_id', 'location_id'], 'stock_balances_product_id_location_id_index');
                $table->index('location_id', 'stock_balances_location_id_foreign');
                $table->index('status', 'stock_balances_status_index');

                // Foreign keys
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade');

                $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('cascade');

                // Unique constraint
                $table->unique(['product_id', 'location_id'], 'stock_balances_product_location_unique');
            });
        }

        // Create customers table if not exists
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('customer_code')->unique();
                $table->string('customer_name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_person_phone')->nullable();
                $table->decimal('credit_limit', 15, 2)->default(0);
                $table->decimal('current_balance', 15, 2)->default(0);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('customer_code');
                $table->index('status');
            });
        }

        // Create sales table if not exists
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->string('sale_number')->unique();
                $table->date('sale_date');
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('location_id');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
                $table->enum('status', ['draft', 'confirmed', 'delivered', 'cancelled'])->default('draft');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->unsignedBigInteger('delivered_by')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('customer_id')->references('id')->on('customers');
                $table->foreign('location_id')->references('id')->on('locations');
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('confirmed_by')->references('id')->on('users');
                $table->foreign('delivered_by')->references('id')->on('users');

                $table->index('sale_number');
                $table->index('sale_date');
                $table->index('customer_id');
                $table->index('location_id');
                $table->index('status');
                $table->index('payment_status');
            });
        }

        // Create sale_details table if not exists
        if (!Schema::hasTable('sale_details')) {
            Schema::create('sale_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sale_id');
                $table->unsignedBigInteger('product_id');
                $table->decimal('quantity', 10, 2);
                $table->decimal('unit_price', 15, 2);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_price', 15, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products');

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
        Schema::dropIfExists('customers');
        Schema::dropIfExists('stock_balances');
    }
};
