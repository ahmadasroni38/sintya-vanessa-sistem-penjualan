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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20)->unique();
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']); // Aktiva, Kewajiban, Modal, Pendapatan, Beban
            $table->enum('normal_balance', ['debit', 'credit']); // Saldo normal akun
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade'); // Untuk hierarki akun
            $table->integer('level')->default(1); // Level hierarki
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0); // Saldo awal
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
