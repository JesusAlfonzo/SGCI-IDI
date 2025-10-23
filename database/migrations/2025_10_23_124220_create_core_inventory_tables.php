<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for auxiliary and product tables.
     */
    public function up(): void
    {
        // 1. Tablas de Atributos Auxiliares
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre de la categoría (ej. Reactivos, Oficina).');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Unidad de medida (ej. Caja, mL, Pza).');
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Ubicación de almacenamiento (ej. Refrigerador C).');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre del proveedor.');
            $table->string('contact_person')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->enum('priority', ['A', 'B', 'C', 'D'])->default('C')->comment('Prioridad del proveedor basada en desempeño.');
            $table->timestamps();
        });

        // 2. Tabla Central de Productos (Inventory)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->unique()->comment('Código Único/SKU (Mandatorio).');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Campos de Stock y Mínimo
            $table->integer('stock_actual')->default(0)->comment('Stock en tiempo real.');
            $table->integer('stock_minimo')->default(0)->comment('Nivel mínimo para alerta.');
            
            // Indicador de Kit
            $table->boolean('is_kit')->default(false)->comment('Indica si el producto es un Kit de Pruebas.');

            // Claves Foráneas de Categorización
            $table->foreignId('category_id')->constrained('categories')->comment('FK a categories.');
            $table->foreignId('unit_id')->constrained('units')->comment('FK a units.');
            $table->foreignId('location_id')->constrained('locations')->comment('FK a locations.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};
