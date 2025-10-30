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
        Schema::table('journal_entry_details', function (Blueprint $table) {
            // Add missing columns for enhanced functionality only if they don't exist
            if (!Schema::hasColumn('journal_entry_details', 'debit_amount')) {
                $table->decimal('debit_amount', 15, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('journal_entry_details', 'credit_amount')) {
                $table->decimal('credit_amount', 15, 2)->default(0)->after('debit_amount');
            }
            if (!Schema::hasColumn('journal_entry_details', 'quantity')) {
                $table->decimal('quantity', 15, 4)->nullable()->after('credit_amount');
            }
            if (!Schema::hasColumn('journal_entry_details', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('journal_entry_details', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('journal_entry_details', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            }
            if (!Schema::hasColumn('journal_entry_details', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('locations')->onDelete('set null')->after('account_id');
            }
            if (!Schema::hasColumn('journal_entry_details', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('assets')->onDelete('set null')->after('department_id');
            }
            if (!Schema::hasColumn('journal_entry_details', 'reconciliation_id')) {
                $table->string('reconciliation_id')->nullable()->after('tax_amount');
            }

            // Add indexes for performance
            try {
                $table->index(['journal_entry_id', 'account_id']);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
            try {
                $table->index(['account_id', 'transaction_type']);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
            try {
                $table->index(['reconciliation_id']);
            } catch (\Exception $e) {
                // Index might already exist, continue
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entry_details', function (Blueprint $table) {
            $table->dropIndex(['journal_entry_id', 'account_id']);
            $table->dropIndex(['account_id', 'transaction_type']);
            $table->dropIndex(['reconciliation_id']);

            $table->dropForeign(['department_id']);
            $table->dropForeign(['project_id']);

            $table->dropColumn([
                'debit_amount',
                'credit_amount',
                'quantity',
                'unit_price',
                'tax_rate',
                'tax_amount',
                'department_id',
                'project_id',
                'reconciliation_id'
            ]);
        });
    }
};
