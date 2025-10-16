<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'CatUsuarios';
    protected $primaryKey = 'IdUsuario';
    public $timestamps = false;

    // ¡¡LA FUNCIÓN getAuthPasswordName() HA SIDO ELIMINADA!!

    protected $fillable = [
        'Nombre',
        'Email',
        'Password', // O 'password', no importa aquí
    ];

    protected $hidden = [
        'password', // O 'password'
    ];
}