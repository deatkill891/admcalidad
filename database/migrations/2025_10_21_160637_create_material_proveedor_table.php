<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Esta tabla no necesita 'id' ni 'timestamps'
        // Solo las dos claves foráneas
        Schema::create('material_proveedor', function (Blueprint $table) {
            
            // Columna para el ID del Material
            $table->unsignedBigInteger('IdMaterial');
            // Columna para el ID del Proveedor
            $table->unsignedBigInteger('IdProveedor');

            // Definimos las llaves foráneas
            $table->foreign('IdMaterial')->references('IdMaterial')->on('materiales')->onDelete('cascade');
            $table->foreign('IdProveedor')->references('IdProveedor')->on('CatProveedores')->onDelete('cascade');

            // Definimos una clave primaria compuesta para evitar duplicados
            $table->primary(['IdMaterial', 'IdProveedor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_proveedor');
    }
};