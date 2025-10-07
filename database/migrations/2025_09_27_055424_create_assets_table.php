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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('good');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'retired', 'lost'])->default('active');
            $table->string('image_path')->nullable();
            $table->json('specifications')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();

            // Foreign keys
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('parent_asset_id')->nullable(); // For hierarchical assets (components)

            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('asset_categories')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('parent_asset_id')->references('id')->on('assets')->onDelete('set null');

            // Indexes for better performance
            $table->index('category_id');
            $table->index('location_id');
            $table->index('parent_asset_id');
            $table->index('status');
            $table->index('condition');
            $table->index(['category_id', 'location_id']);
            $table->index(['status', 'condition']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
