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
        Schema::table('purchases', function (Blueprint $table) {
            // Añadir la columna total_amount (DECIMAL con 10 dígitos en total y 2 decimales)
            // La colocamos después de invoice_number, si ya la agregaste en el paso anterior.
            $table->decimal('total_amount', 10, 2)->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('total_amount');
        });
    }
};