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
        Schema::create('chart_of_account_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->string('event_type'); // created, updated, deleted, restored
            $table->json('old_values')->nullable(); // Previous values before change
            $table->json('new_values')->nullable(); // New values after change
            $table->string('user_id')->nullable(); // User who made the change
            $table->string('user_name')->nullable(); // User name for display
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->string('user_agent')->nullable(); // Browser user agent
            $table->timestamps();

            // Indexes for performance
            $table->index(['chart_of_account_id', 'event_type'], 'idx_audits_account_event');
            $table->index(['user_id', 'event_type'], 'idx_audits_user_event');
            $table->index('created_at', 'idx_audits_created_at');
            $table->index(['chart_of_account_id', 'created_at'], 'idx_audits_account_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_account_audits');
    }
};
