<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_tipos_proceso', function (Blueprint $table) {
            $table->id('IdTipoProceso');
            $table->string('Clave', 50)->unique()->comment('Ej: MUESTRA_COMPLETA, HORNO_HF');
            $table->string('Nombre', 100);
            $table->timestamps();
        });
        // Opcional: Insertar datos iniciales
        // \DB::table('cat_tipos_proceso')->insert([
        //     ['Clave' => 'MUESTRA_COMPLETA', 'Nombre' => 'Análisis de Muestra Finalizado'],
        //     ['Clave' => 'HORNO_HF', 'Nombre' => 'Análisis de Horno HF Finalizado'],
        //     // ... otros tipos de proceso
        // ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_tipos_proceso');
    }
};