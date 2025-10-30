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
        Schema::create('stock_in', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // Nomor transaksi masuk
            $table->date('transaction_date'); // Tanggal transaksi
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->integer('quantity'); // Jumlah masuk
            $table->decimal('unit_price', 15, 2); // Harga satuan
            $table->decimal('total_price', 15, 2); // Total harga
            $table->string('supplier_name')->nullable(); // Nama supplier
            $table->string('reference_number')->nullable(); // Nomor referensi (PO, Invoice, dll)
            $table->text('notes')->nullable(); // Catatan
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
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
        Schema::dropIfExists('stock_in');
    }
};
