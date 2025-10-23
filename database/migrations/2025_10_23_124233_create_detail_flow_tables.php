<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for transaction detail tables.
     */
    public function up(): void
    {
        // 5. Módulo de Entradas - Detalle de Compras
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity')->comment('Cantidad que ingresa al inventario. (Afecta stock_actual).');
            $table->decimal('unit_purchase_price', 10, 4);
            $table->timestamps();
        });

        // 6. Módulo de Salidas - Detalle de Solicitudes
        Schema::create('request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity_requested')->comment('Cantidad solicitada.');
            $table->timestamps();
        });

        // 6. Módulo de Salidas - Aprobación (1:1 con requests)
        Schema::create('request_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->unique()->constrained('requests')->onDelete('cascade');
            $table->foreignId('approver_user_id')->constrained('users')->comment('Usuario que aprobó la solicitud.');
            $table->dateTime('approval_date');
            $table->text('approval_notes')->nullable();
            $table->timestamps();
        });

        // 6. Módulo de Salidas - Detalle de Entrega (Afecta Stock)
        Schema::create('request_delivery_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity_delivered')->comment('Cantidad final entregada. (Reduce stock_actual).');
            $table->foreignId('delivered_by_user_id')->constrained('users')->comment('Usuario que entrega el producto.');
            $table->foreignId('received_by_user_id')->constrained('users')->comment('Usuario que recibe el producto.');
            $table->dateTime('delivery_date');
            $table->text('delivery_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_delivery_details');
        Schema::dropIfExists('request_approvals');
        Schema::dropIfExists('request_details');
        Schema::dropIfExists('purchase_details');
    }
};
