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
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('issue_type', ['hardware', 'software', 'electrical', 'mechanical', 'cosmetic', 'other'])->default('hardware');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('pending');

            // Asset relationship
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id', 'rr_asset_id_foreign')->references('id')->on('assets')->onDelete('cascade');

            // Location relationship (optional - where the repair is taking place)
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id', 'rr_location_id_foreign')->references('id')->on('locations')->onDelete('set null');

            // User relationships
            $table->unsignedBigInteger('requester_id');
            $table->foreign('requester_id', 'rr_requester_id_foreign')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to', 'rr_assigned_to_foreign')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by', 'rr_approved_by_foreign')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('completed_by')->nullable();
            $table->foreign('completed_by', 'rr_completed_by_foreign')->references('id')->on('users')->onDelete('set null');

            // Vendor relationship (optional - for external repairs)
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'rr_vendor_id_foreign')->references('id')->on('vendors')->onDelete('set null');

            // Work Order relationship (optional - if converted to work order)
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->foreign('work_order_id', 'rr_work_order_id_foreign')->references('id')->on('work_orders')->onDelete('set null');

            // Dates
            $table->timestamp('requested_date');
            $table->timestamp('approved_date')->nullable();
            $table->timestamp('started_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->timestamp('due_date')->nullable();

            // Cost and time estimates
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            // Repair details
            $table->text('issue_details')->nullable();
            $table->text('steps_to_reproduce')->nullable();
            $table->text('impact_description')->nullable();
            $table->text('repair_actions')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->text('testing_notes')->nullable();
            $table->json('required_parts')->nullable();
            $table->json('parts_used')->nullable();
            $table->json('tools_required')->nullable();
            $table->json('tools_used')->nullable();

            // Asset condition
            $table->enum('asset_condition_before', ['working', 'partially_working', 'not_working', 'damaged'])->nullable();
            $table->enum('asset_condition_after', ['working', 'partially_working', 'not_working', 'damaged'])->nullable();
            $table->text('condition_notes')->nullable();

            // Documentation
            $table->json('attachments')->nullable();
            $table->json('before_images')->nullable();
            $table->json('after_images')->nullable();
            $table->json('documentation')->nullable();

            // Quality and follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->text('follow_up_notes')->nullable();
            $table->timestamp('follow_up_date')->nullable();
            $table->boolean('customer_satisfied')->nullable();
            $table->integer('satisfaction_rating')->nullable(); // 1-5 scale
            $table->text('feedback')->nullable();

            // Warranty and compliance
            $table->boolean('under_warranty')->default(false);
            $table->string('warranty_claim_number')->nullable();
            $table->boolean('safety_incident')->default(false);
            $table->text('safety_notes')->nullable();
            $table->boolean('requires_calibration')->default(false);
            $table->boolean('environmental_impact')->default(false);
            $table->text('disposal_notes')->nullable();

            // Workflow and approval
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('approval_workflow')->nullable();
            $table->decimal('approval_threshold', 12, 2)->nullable();

            // Performance metrics
            $table->decimal('downtime_hours', 8, 2)->nullable();
            $table->integer('response_time_minutes')->nullable();
            $table->integer('resolution_time_hours')->nullable();
            $table->enum('service_level', ['standard', 'expedited', 'emergency'])->default('standard');

            // Additional metadata
            $table->text('internal_notes')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('external_reference')->nullable();
            $table->boolean('billable')->default(false);
            $table->string('cost_center')->nullable();
            $table->string('budget_code')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index('status', 'rr_status_idx');
            $table->index('priority', 'rr_priority_idx');
            $table->index('severity', 'rr_severity_idx');
            $table->index('issue_type', 'rr_issue_type_idx');
            $table->index('requested_date', 'rr_requested_date_idx');
            $table->index('due_date', 'rr_due_date_idx');
            $table->index(['asset_id', 'status'], 'rr_asset_status_idx');
            $table->index(['requester_id', 'status'], 'rr_requester_status_idx');
            $table->index(['assigned_to', 'status'], 'rr_assigned_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};
