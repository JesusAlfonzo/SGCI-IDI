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
            // Se asume que 'reason' (columna usada para rechazo) NO EXISTE. Si ya existe, Laravel ignorará esta línea.
            // La hacemos NULLABLE para que las solicitudes existentes no fallen.
            $table->text('reason')->nullable()->after('status'); 

            // Columnas usadas para registrar quién y cuándo aprobó/rechazó
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->after('reason')->comment('Usuario que aprueba o rechaza la solicitud.');
            $table->dateTime('approval_date')->nullable()->after('approved_by_user_id')->comment('Fecha de aprobación o rechazo.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by_user_id');
            $table->dropColumn('approval_date');
            $table->dropColumn('reason'); // Eliminar también la columna 'reason'
        });
    }
};