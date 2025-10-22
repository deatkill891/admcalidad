<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// ¡Importante! Asegúrate que extiende Authenticatable para que Auth::user() funcione
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // Si usas notificaciones

// ¡Importa el modelo Permiso!
use App\Models\Permiso;


class Usuario extends Authenticatable // Verifica que extienda Authenticatable
{
    use HasFactory, Notifiable; // Añade los traits que uses

    // Especifica la tabla si no sigue la convención de nombres de Laravel
    protected $table = 'CatUsuarios';

    // Especifica la clave primaria si no es 'id'
    protected $primaryKey = 'IdUsuario';

    // Desactiva timestamps si tu tabla no tiene created_at/updated_at
    public $timestamps = false; // Basado en tu SQL, parece que solo tienes FechaRegistro

    // Define las columnas que se pueden asignar masivamente (si aplica)
    protected $fillable = [
        'username',
        'email',
        'password', // Asegúrate de que los campos coincidan con tu tabla
        'Pista',
        'ImgUsuario',
        'IdUbicacion',
        'IdTipoUsuario',
        'IdEstatus',
        'ShowLock',
    ];

    // Define los campos que deben ocultarse (si aplica)
    protected $hidden = [
        'password',
        // 'remember_token', // Tu tabla CatUsuarios no parece tener remember_token
    ];

    // Define casts si necesitas convertir tipos de datos (si aplica)
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Tu tabla no tiene esto
        'password' => 'hashed', // Si usas hashing de Laravel 10+
    ];


    /**
     * Obtiene la fila de permisos asociada a este usuario desde CatPermisos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function permiso()
    {
        // La relación:
        // - Modelo relacionado: Permiso::class
        // - Llave foránea en CatPermisos: 'IdUsuario'
        // - Llave local (primaria) en CatUsuarios: 'IdUsuario' (nuestra $primaryKey)
        return $this->hasOne(Permiso::class, 'IdUsuario', $this->getKeyName());
        // Usar $this->getKeyName() es más robusto que escribir 'IdUsuario' directamente
    }

    // --- Otras relaciones que ya tenías ---
    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'IdTipoUsuario');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'IdUbicacion');
    }
    // --- Fin otras relaciones ---
}