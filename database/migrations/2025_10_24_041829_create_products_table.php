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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50)->unique();
            $table->string('product_name', 255);
            $table->text('description')->nullable();
            $table->enum('product_type', ['raw_material', 'finished_goods', 'consumable'])->default('raw_material');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('product_code');
            $table->index('product_name');
            $table->index('product_type');
            $table->index('category_id');
            $table->index('unit_id');
            $table->index('is_active');
            $table->index('created_at');
            $table->index(['product_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
