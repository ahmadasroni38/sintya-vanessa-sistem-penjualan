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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['maintenance', 'repair', 'installation', 'inspection', 'upgrade', 'replacement']);
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['draft', 'pending', 'assigned', 'in_progress', 'on_hold', 'completed', 'cancelled']);

            // Asset relationship
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('set null');

            // Location relationship
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');

            // User relationships
            $table->unsignedBigInteger('requester_id');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            // Dates and times
            $table->datetime('requested_date');
            $table->datetime('due_date')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();

            // Work details
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();

            // Additional information
            $table->json('materials_needed')->nullable();
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();

            // Recurring work order
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // daily, weekly, monthly, yearly
            $table->integer('recurring_interval')->nullable(); // every X frequency
            $table->datetime('next_occurrence')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'priority']);
            $table->index(['asset_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['requester_id']);
            $table->index(['location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
