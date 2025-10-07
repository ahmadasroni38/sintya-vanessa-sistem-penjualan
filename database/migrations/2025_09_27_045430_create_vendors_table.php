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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('company_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Indonesia');
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->enum('vendor_type', ['supplier', 'service_provider', 'contractor', 'other'])->default('supplier');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->enum('payment_terms', ['cash', 'net_15', 'net_30', 'net_45', 'net_60', 'custom'])->default('net_30');
            $table->string('tax_id')->nullable(); // NPWP
            $table->json('metadata')->nullable(); // For storing additional attributes
            $table->timestamps();

            // Indexes for better performance
            $table->index('is_active');
            $table->index('vendor_type');
            $table->index(['name', 'is_active']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
