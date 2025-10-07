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
        Schema::create('preventive_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('maintenance_type', ['inspection', 'cleaning', 'lubrication', 'calibration', 'replacement', 'testing', 'adjustment', 'other']);
            $table->enum('priority', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['active', 'inactive', 'suspended', 'completed']);

            // Asset relationship
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');

            // Location relationship (optional - can be derived from asset)
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');

            // User relationships
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            // Schedule Information
            $table->enum('frequency_type', ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual', 'custom']);
            $table->integer('frequency_value')->default(1); // Every X frequency_type
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_due_date');
            $table->date('last_completed_date')->nullable();

            // Custom frequency settings
            $table->json('custom_frequency_settings')->nullable(); // For complex schedules
            $table->json('skip_dates')->nullable(); // Dates to skip (holidays, etc.)

            // Work details
            $table->decimal('estimated_duration_hours', 8, 2)->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->json('required_materials')->nullable();
            $table->json('required_tools')->nullable();
            $table->json('safety_requirements')->nullable();

            // Instructions and procedures
            $table->text('work_instructions')->nullable();
            $table->json('checklist_items')->nullable(); // Array of checklist items
            $table->text('safety_notes')->nullable();
            $table->json('attachments')->nullable();

            // Compliance and regulations
            $table->string('compliance_standard')->nullable(); // ISO, OSHA, etc.
            $table->boolean('requires_certification')->default(false);
            $table->string('certification_required')->nullable();

            // Tracking and metrics
            $table->integer('total_occurrences')->default(0);
            $table->integer('completed_occurrences')->default(0);
            $table->integer('missed_occurrences')->default(0);
            $table->decimal('average_completion_time', 8, 2)->nullable();
            $table->decimal('average_cost', 10, 2)->nullable();

            // Performance tracking
            $table->decimal('compliance_rate', 5, 2)->default(0); // Percentage
            $table->date('last_updated_date')->nullable();

            // Notification settings
            $table->json('notification_settings')->nullable(); // When to send notifications
            $table->boolean('auto_create_work_orders')->default(false);
            $table->integer('advance_notice_days')->default(7);

            // Additional metadata
            $table->text('notes')->nullable();
            $table->boolean('is_template')->default(false); // Can be used as template for other assets
            $table->unsignedBigInteger('template_source_id')->nullable(); // If created from template
            $table->foreign('template_source_id')->references('id')->on('preventive_maintenances')->onDelete('set null');

            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'next_due_date']);
            $table->index(['asset_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['frequency_type', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['compliance_rate']);
            $table->index(['next_due_date']);
            $table->index(['created_by']);
            $table->index(['location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenances');
    }
};
