<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla si no sigue la convención de Laravel
    protected $table = 'CatPermisos';

    // Especifica la clave primaria si no es 'id'
    protected $primaryKey = 'IdPermiso';

    // Desactiva los timestamps si tu tabla no tiene 'created_at' y 'updated_at'
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'IdUsuario',
        'IdEstatus',
        'Administrador',
        'Analisis',
        'Muestreo',
        'Insumos',
        'PolvoZn',
        'Chatarra',
        'CqElementos',
        'CqOxidos',
        'Metrologia',
        'Eads',
        'Evidencias',
        'FechaRegistro',
        'FechaActualizacion',
    ];

    /**
     * Define la relación inversa con el modelo Usuario.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario', 'IdUsuario');
    }
}