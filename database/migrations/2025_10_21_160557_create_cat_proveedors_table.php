<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CatProveedores', function (Blueprint $table) {
            $table->id('IdMaterial'); // Usamos 'IdProveedor' como PK
            $table->string('NombreProveedor', 255);
            $table->string('RFC', 13)->nullable();
            // Agrega aquí otros campos que necesites (ej. email, telefono)
            $table->boolean('Estatus')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CatProveedores');
    }
};