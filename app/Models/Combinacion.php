<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combinacion extends Model
{
    use HasFactory;
    protected $table = 'CatConvinaciones';
    // Esta tabla tiene una clave primaria compuesta, Laravel lo manejará.
    public $timestamps = false;

    // Relación: Una combinación pertenece a un Elemento
    public function elemento()
    {
        return $this->belongsTo(Elemento::class, 'IdElemento', 'IdElemento');
    }
}