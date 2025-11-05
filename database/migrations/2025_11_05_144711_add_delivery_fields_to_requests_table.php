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
        Schema::table('requests', function (Blueprint $table) {
            // Columna para registrar al personal de almacén que entrega
            $table->foreignId('warehouse_staff_id')
                  ->nullable()
                  ->constrained('users')
                  ->after('approved_by_user_id')
                  ->comment('Usuario de Almacén que procesa la entrega.');

            // Columna para registrar la fecha de la entrega final
            $table->dateTime('delivery_date')
                  ->nullable()
                  ->after('approval_date')
                  ->comment('Fecha de la entrega final del material.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_staff_id');
            $table->dropColumn('delivery_date');
        });
    }
};
