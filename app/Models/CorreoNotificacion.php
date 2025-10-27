<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoNotificacion extends Model
{
    use HasFactory;

    protected $table = 'cat_correos_notificacion';
    protected $primaryKey = 'IdCorreo';
    protected $fillable = ['Correo', 'NombreDestinatario', 'Activo'];

    public function tiposProceso()
    {
        return $this->belongsToMany(TipoProceso::class, 'proceso_correo', 'IdCorreo', 'IdTipoProceso');
    }
}