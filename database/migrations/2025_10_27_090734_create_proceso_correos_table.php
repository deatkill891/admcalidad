<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proceso_correo', function (Blueprint $table) {
            $table->id('IdProcesoCorreo');
            $table->foreignId('IdTipoProceso')->constrained('cat_tipos_proceso', 'IdTipoProceso');
            $table->foreignId('IdCorreo')->constrained('cat_correos_notificacion', 'IdCorreo');
            $table->boolean('Activo')->default(true);
            $table->unique(['IdTipoProceso', 'IdCorreo']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proceso_correo');
    }
};