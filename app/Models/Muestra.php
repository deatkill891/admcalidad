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
        'Analisis', 'FechaRegistro', 'FechaRecibo', 'Clima', 'Humedad'
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
}