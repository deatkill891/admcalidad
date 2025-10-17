<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoAnalisis extends Model
{
    use HasFactory;
    protected $table = 'OperResultadoAnalisis';
    protected $primaryKey = 'IdResultado';
    public $timestamps = false;

    protected $fillable = [
        'IdMuestra', 'IdElemento', 'Valor', 'FechaRegistro'
    ];

    /**
     * Define la relación: Un resultado pertenece a un Elemento.
     * ESTA ES LA FUNCIÓN QUE FALTABA.
     */
    public function elemento()
    {
        // Esto le dice a Laravel que este modelo (ResultadoAnalisis)
        // pertenece a un modelo Elemento, usando la clave foránea 'IdElemento'.
        return $this->belongsTo(Elemento::class, 'IdElemento', 'IdElemento');
    }
}