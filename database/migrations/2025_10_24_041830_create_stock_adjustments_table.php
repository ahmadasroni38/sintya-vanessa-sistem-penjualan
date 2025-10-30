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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique(); // Nomor adjustment
            $table->date('adjustment_date'); // Tanggal adjustment
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->integer('system_quantity'); // Jumlah di sistem
            $table->integer('actual_quantity'); // Jumlah fisik/aktual
            $table->integer('difference_quantity'); // Selisih (actual - system)
            $table->enum('adjustment_type', ['increase', 'decrease']); // Jenis adjustment
            $table->string('reason')->nullable(); // Alasan adjustment
            $table->text('notes')->nullable(); // Catatan
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
