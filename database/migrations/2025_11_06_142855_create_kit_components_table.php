<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🎯 Tabla Pivote: Define la "receta" del Kit
        Schema::create('kit_components', function (Blueprint $table) {
            
            // kit_id: FK al KIT (el producto que es definido como kit, tabla 'kits')
            $table->foreignId('kit_id')->constrained('kits')->onDelete('cascade');
            
            // component_id: FK al COMPONENTE (el producto que se consume, tabla 'products')
            $table->foreignId('component_id')->constrained('products')->onDelete('cascade');
            
            // La cantidad de este componente que se requiere para UN kit.
            $table->integer('quantity')->comment('Cantidad necesaria por cada kit consumido');
            
            // Clave Primaria Compuesta: Un kit no puede tener el mismo componente dos veces.
            $table->primary(['kit_id', 'component_id']);
            // Nota: No se requiere timestamps en tablas pivote simples
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_components');
    }
};