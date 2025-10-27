<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_correos_notificacion', function (Blueprint $table) {
            $table->id('IdCorreo');
            $table->string('Correo', 100)->unique();
            $table->string('NombreDestinatario', 100);
            $table->boolean('Activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_correos_notificacion');
    }
};