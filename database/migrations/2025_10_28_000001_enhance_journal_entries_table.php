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
        Schema::table('journal_entries', function (Blueprint $table) {
            // Add missing columns for enhanced functionality
            $table->decimal('total_debit', 15, 2)->default(0)->after('status');
            $table->decimal('total_credit', 15, 2)->default(0)->after('total_debit');
            $table->string('currency', 3)->default('IDR')->after('total_credit');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->after('currency');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('posted_by');
            $table->timestamp('approved_at')->nullable()->after('posted_at');
            $table->json('metadata')->nullable()->after('approved_at');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');

            // Add indexes for performance
            $table->index(['entry_date', 'status']);
            $table->index(['entry_type', 'status']);
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['entry_date', 'status']);
            $table->dropIndex(['entry_type', 'status']);
            $table->dropIndex(['created_by', 'status']);

            $table->dropForeign(['approved_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'total_debit',
                'total_credit',
                'currency',
                'exchange_rate',
                'approved_by',
                'approved_at',
                'metadata',
                'updated_by'
            ]);
        });
    }
};
