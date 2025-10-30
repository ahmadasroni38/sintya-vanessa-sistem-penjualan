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
        Schema::create('account_balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->decimal('balance', 15, 2);
            $table->decimal('debit_total', 15, 2);
            $table->decimal('credit_total', 15, 2);
            $table->date('period_start');
            $table->date('period_end');
            $table->string('calculated_by')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['chart_of_account_id', 'period_start'], 'idx_balance_histories_account_period_start');
            $table->index(['chart_of_account_id', 'period_end'], 'idx_balance_histories_account_period_end');
            $table->index('period_start', 'idx_balance_histories_period_start');
            $table->index('period_end', 'idx_balance_histories_period_end');
            $table->index('calculated_by', 'idx_balance_histories_calculated_by');
            $table->index('created_at', 'idx_balance_histories_created_at');

            // Unique constraint to prevent duplicate calculations
            $table->unique(['chart_of_account_id', 'period_start', 'period_end'], 'unique_balance_histories_account_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balance_histories');
    }
};
