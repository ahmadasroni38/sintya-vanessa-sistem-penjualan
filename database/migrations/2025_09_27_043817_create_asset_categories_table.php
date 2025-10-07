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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366F1'); // Hex color for UI
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable(); // For hierarchical categories
            $table->json('metadata')->nullable(); // For storing additional attributes
            $table->timestamps();

            // Foreign key constraint for parent category
            $table->foreign('parent_id')->references('id')->on('asset_categories')->onDelete('set null');

            // Indexes for better performance
            $table->index('is_active');
            $table->index('parent_id');
            $table->index(['name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
