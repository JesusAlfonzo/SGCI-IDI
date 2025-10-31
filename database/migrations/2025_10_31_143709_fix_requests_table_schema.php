<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // 🔑 1. Añadir 'request_code' (EL CAMPO QUE FALTABA)
            $table->string('request_code', 100)->unique()->after('id');

            // 🔑 2. Renombrar 'requester_user_id' a 'requested_by_user_id'
            // Esto es necesario para que coincida con el RequestController.
            $table->renameColumn('requester_user_id', 'requested_by_user_id'); 

            // 🔑 3. Renombrar 'reason' a 'purpose'
            // Esto es necesario para que coincida con el RequestController/FormRequest.
            $table->renameColumn('reason', 'purpose');
            
            // 💡 NOTA: También podrías cambiar el 'status' de Enum para usar mayúsculas 
            // ('PENDIENTE') para coincidir con tu lógica de Laravel, aunque MySQL es menos estricto aquí.
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // Revertir cambios
            $table->dropColumn('request_code');
            $table->renameColumn('requested_by_user_id', 'requester_user_id');
            $table->renameColumn('purpose', 'reason');
        });
    }
};