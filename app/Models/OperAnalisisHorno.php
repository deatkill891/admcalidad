<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperAnalisisHorno extends Model
{
    use HasFactory;

    protected $table = 'OperAnalisisHorno';
    protected $primaryKey = 'IdRegistro';
    public $timestamps = false; // Tu tabla no tiene created_at/updated_at

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'Fecha',
        'Tecnico',
        'HORNO',
        'Turno',
        'COLADA',
        'GRADO',
        'CaO',
        'MgO',
        'SiO2',
        'Al2O3',
        'MnO',
        'FeO',
        'S',
        'IB2',
        'IB3',
        'IB4',
        'TOTAL',
        'KgCalSiderurgica',
        'KgCalDolomitica',
        'IdUsuario',
        'NombreUsuario',
        'IdTipoAnalisis',
        'TipoMuestra',
        'IdEstatusAnalisis',
    ];

    /**
     * Definir el formato de fecha para la columna 'Fecha'.
     */
    protected $casts = [
        'Fecha' => 'datetime',
    ];
}