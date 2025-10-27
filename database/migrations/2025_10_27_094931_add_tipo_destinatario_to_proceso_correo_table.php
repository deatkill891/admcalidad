<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proceso_correo', function (Blueprint $table) {
            // Añadimos la columna después de IdCorreo
            // 'to' = Principal, 'cc' = Copia. Podrías añadir 'bcc' si lo necesitas.
            $table->enum('TipoDestinatario', ['to', 'cc'])->default('to')->after('IdCorreo');

            // Modificamos la llave única para incluir el tipo
            // (Un mismo correo puede estar en TO y CC para procesos DIFERENTES,
            // pero no puede ser TO y CC para el MISMO proceso)
            $table->dropUnique(['IdTipoProceso', 'IdCorreo']);
            $table->unique(['IdTipoProceso', 'IdCorreo', 'TipoDestinatario']);
        });
    }

    public function down(): void
    {
        Schema::table('proceso_correo', function (Blueprint $table) {
            // Revertimos los cambios
            $table->dropUnique(['IdTipoProceso', 'IdCorreo', 'TipoDestinatario']);
            $table->unique(['IdTipoProceso', 'IdCorreo']);
            $table->dropColumn('TipoDestinatario');
        });
    }
};