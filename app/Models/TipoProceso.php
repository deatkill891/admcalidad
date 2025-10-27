<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProceso extends Model
{
    use HasFactory;

    protected $table = 'cat_tipos_proceso';
    protected $primaryKey = 'IdTipoProceso';
    protected $fillable = ['Clave', 'Nombre', 'Descripcion'];

    public function correos()
    {
        return $this->belongsToMany(CorreoNotificacion::class, 'proceso_correo', 'IdTipoProceso', 'IdCorreo');
    }
}