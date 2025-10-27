<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Asegúrate de importar los modelos relacionados que usas
use App\Models\Material;
use App\Models\EstatusAnalisis;
use App\Models\Usuario; // O User::class si usas el modelo User por defecto de Laravel/Breeze
// use App\Models\Proveedor; // Necesitarías crear este modelo si quieres la relación
// use App\Models\Ubicacion; // Necesitarías crear este modelo si quieres la relación
use App\Models\ResultadoAnalisis;
use App\Models\Combinacion; // Asegúrate que este modelo exista si usas la relación combinaciones

class Muestra extends Model
{
    use HasFactory;

    // Nombre correcto de la tabla según tu código
    protected $table = 'OperMuestras';

    // Clave primaria correcta según tu código
    protected $primaryKey = 'IdMuestra';

    // Timestamps: Tu código indica false, lo cual está bien si no tienes 'created_at' y 'updated_at'
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Campos que ya tenías y que coinciden con MuestraController@store
        'IdMaterial',
        'IdEstatusAnalisis',
        'IdUsuarioOper',
        'PlacaTractor',
        'PlacaTolva',
        'Proveedor',
        'Remision',
        'Tonelaje',
        'Solicitante',
        'Area',
        'Identificacion',
        'Analisis',
        'FechaRegistro',    // Necesario porque se asigna en create()
        'FechaRecibo',      // Necesario porque se asigna en create()

        // Campos que se asignan en MuestraController@storeAnalisis
        // Es buena práctica incluirlos aunque no se usen en el 'create' inicial
        'Clima',
        'Humedad',
        'IdUsuarioAnal',
        'FechaAnalisis'
    ];

    // --- RELACIONES ---

    // Relación: Una Muestra pertenece a un Material
    public function material() {
        // Correcto si el modelo se llama Material y ambas claves son IdMaterial
        return $this->belongsTo(Material::class, 'IdMaterial', 'IdMaterial');
    }

    // Relación: Una Muestra tiene un Estatus de Análisis
    public function estatusAnalisis() {
        // Correcto si el modelo se llama EstatusAnalisis y ambas claves son IdEstatusAnalisis
        return $this->belongsTo(EstatusAnalisis::class, 'IdEstatusAnalisis', 'IdEstatusAnalisis');
    }

    // Relación: Una Muestra fue registrada por un Usuario (Operador)
    public function usuarioOper() {
        // ¡Importante! Asegúrate que 'Usuario::class' sea el modelo correcto (puede ser User::class)
        // y que la clave primaria en la tabla de usuarios sea 'IdUsuario'. Si es 'id', cámbialo.
        return $this->belongsTo(Usuario::class, 'IdUsuarioOper', 'IdUsuario');
    }

    // Relación: Una Muestra fue analizada por un Usuario (Analista) - Añadida para completar
     public function usuarioAnal() {
        // ¡Importante! Asegúrate que 'Usuario::class' sea el modelo correcto (puede ser User::class)
        // y que la clave primaria en la tabla de usuarios sea 'IdUsuario'. Si es 'id', cámbialo.
        return $this->belongsTo(Usuario::class, 'IdUsuarioAnal', 'IdUsuario');
     }

    // Relación: Para el formulario de análisis dinámico (hasManyThrough)
    // Esta relación parece compleja y depende de cómo estén estructuradas tus tablas
    // Material, Combinacion y Elemento. Revisa que las claves foráneas y locales sean correctas.
    public function combinaciones()
    {
        return $this->hasManyThrough(
            Combinacion::class, // Modelo final
            Material::class,    // Modelo intermedio
            'IdMaterial',       // Clave foránea en tabla Materiales (relaciona Muestra -> Material)
            'IdMaterial',       // Clave foránea en tabla Combinaciones (relaciona Material -> Combinacion)
            'IdMaterial',       // Clave local en tabla Muestras (OperMuestras)
            'IdMaterial'        // Clave local en tabla Materiales (CatMateriales)
        )->with('elemento');    // Cargar también la información del elemento asociado (requiere relación 'elemento' en Combinacion)
    }

    /**
     * Define la relación con los resultados del análisis.
     */
    public function resultados()
    {
        // Correcto si el modelo es ResultadoAnalisis y las claves son IdMuestra
        return $this->hasMany(ResultadoAnalisis::class, 'IdMuestra', 'IdMuestra')
                    ->with('elemento'); // Carga la relación 'elemento' definida en ResultadoAnalisis
    }

    // --- RELACIONES FALTANTES PARA LA ETIQUETA ---
    // Estas relaciones se intentaron cargar en MuestraController@mostrarEtiqueta.
    // Necesitas definirlas si quieres usarlas.

    /**
     * Relación: Una Muestra pertenece a un Proveedor.
     * NOTA: Esto asume que tienes un campo 'IdProveedor' en tu tabla 'OperMuestras'
     * y un modelo 'Proveedor' con clave primaria 'IdProveedor'.
     * Si guardas el *nombre* del proveedor en 'OperMuestras.Proveedor', esta relación
     * belongsTo no funcionará directamente así. Necesitarías ajustar o normalizar.
     */
    /* // Descomenta y ajusta si tienes IdProveedor
    public function proveedor() {
        // return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
        // O si usas el nombre como clave (menos recomendado):
        // return $this->belongsTo(Proveedor::class, 'Proveedor', 'NombreProveedor'); // Ajusta 'NombreProveedor' al campo clave en Proveedores
    }
    */

    /**
     * Relación: Una Muestra tiene una Ubicacion.
     * NOTA: Esto asume que tienes un campo 'IdUbicacion' en tu tabla 'OperMuestras'
     * y un modelo 'Ubicacion' con clave primaria 'IdUbicacion'.
     */
    /* // Descomenta y ajusta si tienes IdUbicacion
    public function ubicacion() {
        // return $this->belongsTo(Ubicacion::class, 'IdUbicacion', 'IdUbicacion');
    }
    */
}