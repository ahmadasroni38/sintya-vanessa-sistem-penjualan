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
        Schema::create('preventive_maintenance_executions', function (Blueprint $table) {
            $table->id();
            $table->string('execution_code')->unique();

            // Reference to the preventive maintenance schedule
            $table->unsignedBigInteger('preventive_maintenance_id');
            $table->foreign('preventive_maintenance_id', 'pm_exec_pm_id_foreign')->references('id')->on('preventive_maintenances')->onDelete('cascade');

            // Reference to generated work order (if auto-created)
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id', 'pm_exec_wo_id_foreign')->references('id')->on('work_orders')->onDelete('set null');

            // Execution details
            $table->date('scheduled_date');
            $table->date('due_date');
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'skipped', 'overdue', 'cancelled']);

            // User assignments
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to', 'pm_exec_assigned_foreign')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('completed_by')->nullable();
            $table->foreign('completed_by', 'pm_exec_completed_foreign')->references('id')->on('users')->onDelete('set null');

            // Execution results
            $table->decimal('actual_duration_hours', 8, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->json('checklist_results')->nullable(); // Results of checklist items
            $table->json('materials_used')->nullable();
            $table->json('tools_used')->nullable();

            // Findings and observations
            $table->text('work_performed')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('asset_condition', ['excellent', 'good', 'fair', 'poor', 'critical'])->nullable();
            $table->text('issues_found')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->text('follow_up_notes')->nullable();

            // Compliance and certification
            $table->boolean('compliance_verified')->default(false);
            $table->string('certification_number')->nullable();
            $table->date('certification_valid_until')->nullable();

            // Attachments and documentation
            $table->json('attachments')->nullable(); // Photos, documents, etc.
            $table->json('before_photos')->nullable();
            $table->json('after_photos')->nullable();

            // Performance metrics
            $table->boolean('completed_on_time')->default(false);
            $table->integer('days_early_late')->default(0); // Negative for early, positive for late
            $table->decimal('efficiency_rating', 3, 2)->nullable(); // 0-100 rating

            // Weather/environmental conditions (if applicable)
            $table->json('environmental_conditions')->nullable();

            // Additional notes
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['preventive_maintenance_id', 'status'], 'pm_exec_pm_status_idx');
            $table->index(['scheduled_date', 'status'], 'pm_exec_sched_status_idx');
            $table->index(['due_date', 'status'], 'pm_exec_due_status_idx');
            $table->index(['assigned_to', 'status'], 'pm_exec_assigned_status_idx');
            $table->index(['completed_by'], 'pm_exec_completed_by_idx');
            $table->index(['status', 'completed_at'], 'pm_exec_status_completed_idx');
            $table->index(['work_order_id'], 'pm_exec_work_order_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preventive_maintenance_executions');
    }
};
