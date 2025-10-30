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
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->date('transaction_date'); // Tanggal transaksi
            $table->string('transaction_type'); // Jenis transaksi (stock_in, mutation_in, mutation_out, adjustment, opname, etc)
            $table->unsignedBigInteger('reference_id')->nullable(); // ID referensi dari tabel asal
            $table->string('reference_number')->nullable(); // Nomor referensi
            $table->integer('quantity_in')->default(0); // Jumlah masuk
            $table->integer('quantity_out')->default(0); // Jumlah keluar
            $table->integer('balance'); // Saldo stok
            $table->decimal('unit_price', 15, 2)->default(0); // Harga satuan
            $table->text('notes')->nullable(); // Catatan
            $table->timestamps();

            // Index untuk performa query
            $table->index(['product_id', 'location_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_cards');
    }
};
