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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'IdUbicacion',     // <-- Añadido
        'IdTipoUsuario',   // <-- Añadido
        'IdEstatus',       // <-- Añadido
        'FechaRegistro',   // <-- Añadido
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Define la relación uno a uno con el modelo Permiso.
     */
    public function permisos()
    {
        return $this->hasOne(Permiso::class, 'IdUsuario', 'IdUsuario');
    }
}