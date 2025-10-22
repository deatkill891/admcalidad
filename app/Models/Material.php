<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'CatMateriales';
    protected $primaryKey = 'IdMaterial';
    public $timestamps = false; 

    public function estatus() {
        return $this->belongsTo(Estatus::class, 'IdEstatus');
    }

    public function combinaciones() {
        return $this->hasMany(Combinacion::class, 'IdMaterial');
    }

    // --- ¡AGREGA ESTA FUNCIÓN! ---
    /**
     * Obtiene los proveedores asociados con este material.
     */
    public function proveedores()
    {
        // Un Material tiene muchos registros en la tabla CatProveedores
        return $this->hasMany(Proveedor::class, 'IdMaterial', 'IdMaterial');
    }
    // --- FIN DE LA FUNCIÓN ---
}