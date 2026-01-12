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
        Schema::table('sale_details', function (Blueprint $table) {
            // Check if old column names exist before renaming
            if (Schema::hasColumn('sale_details', 'discount_percentage')) {
                $table->renameColumn('discount_percentage', 'discount_percent');
            }
            if (Schema::hasColumn('sale_details', 'tax_percentage')) {
                $table->renameColumn('tax_percentage', 'tax_percent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_details', function (Blueprint $table) {
            // Revert back to old column names
            if (Schema::hasColumn('sale_details', 'discount_percent')) {
                $table->renameColumn('discount_percent', 'discount_percentage');
            }
            if (Schema::hasColumn('sale_details', 'tax_percent')) {
                $table->renameColumn('tax_percent', 'tax_percentage');
            }
        });
    }
};
