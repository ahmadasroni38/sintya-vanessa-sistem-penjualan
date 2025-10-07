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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('color', 7)->default('#10B981'); // Hex color for UI (green theme for locations)
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable(); // For hierarchical locations
            $table->json('metadata')->nullable(); // For storing additional attributes
            $table->timestamps();

            // Foreign key constraint for parent location
            $table->foreign('parent_id')->references('id')->on('locations')->onDelete('set null');

            // Indexes for better performance
            $table->index('is_active');
            $table->index('parent_id');
            $table->index(['name', 'is_active']);
            $table->index(['city', 'is_active']);
            $table->index(['country', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
