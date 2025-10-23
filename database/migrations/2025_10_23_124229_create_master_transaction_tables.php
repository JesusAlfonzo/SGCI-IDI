<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for transaction master tables (purchases and requests).
     */
    public function up(): void
    {
        // 5. Módulo de Entradas - Maestra de Compras
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code', 100)->unique()->comment('Código Único de Factura/OC.');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('registered_by_user_id')->constrained('users')->comment('Usuario que registró la compra.');
            $table->date('purchase_date')->comment('Fecha de la factura.');
            $table->timestamps();
        });

        // 6. Módulo de Salidas - Maestra de Solicitudes
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_user_id')->constrained('users')->comment('Usuario que realiza la solicitud.');
            $table->dateTime('request_date');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Delivered'])->default('Pending')->comment('Estado de la solicitud.');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
        Schema::dropIfExists('purchases');
    }
};
