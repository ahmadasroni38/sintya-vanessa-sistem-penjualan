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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // Nomor jurnal (auto-generated)
            $table->date('entry_date'); // Tanggal transaksi
            $table->string('reference_number')->nullable(); // Nomor referensi (invoice, etc)
            $table->text('description'); // Keterangan transaksi
            $table->enum('entry_type', ['general', 'adjustment', 'closing', 'opening'])->default('general'); // Jenis jurnal
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft'); // Status jurnal
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
