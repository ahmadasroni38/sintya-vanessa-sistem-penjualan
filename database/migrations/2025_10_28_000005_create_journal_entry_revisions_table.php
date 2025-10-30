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
        Schema::create('journal_entry_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->integer('revision_number');
            $table->json('changes');
            $table->foreignId('revised_by')->constrained('users')->onDelete('cascade');
            $table->text('revision_notes')->nullable();
            $table->timestamp('created_at');

            // Add indexes for performance
            $table->index(['journal_entry_id', 'revision_number']);
            $table->index(['revised_by']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_revisions');
    }
};
