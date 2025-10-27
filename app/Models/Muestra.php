<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Muestra extends Model
{
    use HasFactory;
    protected $table = 'OperMuestras';
    protected $primaryKey = 'IdMuestra';
    public $timestamps = false;

    // Campos que se pueden llenar desde el formulario
    protected $fillable = [
        'IdMaterial', 'IdEstatusAnalisis', 'IdUsuarioOper',
        'PlacaTractor', 'PlacaTolva', 'Proveedor', 'Remision',
        'Tonelaje', 'Solicitante', 'Area', 'Identificacion',
        'Analisis', 'FechaRegistro', 'FechaRecibo', 'Clima', 'Humedad',
        'IdUsuarioAnal', 'FechaAnalisis'
    ];

    // Relación: Una Muestra pertenece a un Material
    public function material() {
        return $this->belongsTo(Material::class, 'IdMaterial', 'IdMaterial');
    }

    // Relación: Una Muestra tiene un Estatus de Análisis
    public function estatusAnalisis() {
        return $this->belongsTo(EstatusAnalisis::class, 'IdEstatusAnalisis', 'IdEstatusAnalisis');
    }

    // Relación: Una Muestra fue registrada por un Usuario
    public function usuarioOper() {
        return $this->belongsTo(Usuario::class, 'IdUsuarioOper', 'IdUsuario');
    }
    
    // Relación: Para el formulario de análisis dinámico
    public function combinaciones()
    {
        return $this->hasManyThrough(
            Combinacion::class,
            Material::class,
            'IdMaterial', // Clave foránea en la tabla Materiales
            'IdMaterial', // Clave foránea en la tabla Combinaciones
            'IdMaterial', // Clave local en la tabla Muestras
            'IdMaterial'  // Clave local en la tabla Materiales
        )->with('elemento'); // Cargar también la información del elemento asociado
    }

    /**
     * Define la relación con los resultados del análisis.
     */
    public function resultados()
    {
        // Una muestra tiene muchos resultados
        return $this->hasMany(ResultadoAnalisis::class, 'IdMuestra', 'IdMuestra')
                    ->with('elemento'); // Carga también la info del elemento
    }
}