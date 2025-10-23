<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for kit and price history tables.
     */
    public function up(): void
    {
        // 3. Historial de Precios de Compra
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->comment('FK al producto.');
            $table->foreignId('supplier_id')->constrained('suppliers')->comment('FK al proveedor.');
            $table->decimal('price', 10, 4)->comment('Precio unitario de compra.');
            $table->dateTime('recorded_at')->comment('Fecha de registro del precio.');
            $table->boolean('is_latest')->default(true)->index()->comment('Indica el precio más reciente para la combinación P/S.');
            $table->timestamps();

            // Clave única compuesta para evitar duplicados históricos exactos, aunque el 'is_latest' es el mecanismo principal
            $table->unique(['product_id', 'supplier_id', 'recorded_at'], 'product_supplier_price_unique');
        });

        // 4. Kits de Pruebas - Maestro
        Schema::create('kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->comment('FK al producto que es un kit (1:1).');
            $table->integer('total_usages')->comment('Número total de usos posibles al entrar al inventario.');
            $table->timestamps();
        });

        // 4. Kits de Pruebas - Usos
        Schema::create('kit_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kit_id')->constrained('kits')->comment('FK al kit usado.');
            $table->foreignId('used_by_user_id')->constrained('users')->comment('FK al usuario que registró el uso.');
            $table->dateTime('usage_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kit_usages');
        Schema::dropIfExists('kits');
        Schema::dropIfExists('product_prices');
    }
};
