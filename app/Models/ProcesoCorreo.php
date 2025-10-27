<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoCorreo extends Model
{
    use HasFactory;

    protected $table = 'proceso_correo';
    protected $primaryKey = 'IdProcesoCorreo';
    // Asegúrate de que 'TipoDestinatario' esté en $fillable
    protected $fillable = ['IdTipoProceso', 'IdCorreo', 'TipoDestinatario'];


    // Relaciones
    public function correo()
    {
        // Asegúrate que CorreoNotificacion use 'IdCorreo' como primaryKey si no es 'id'
        return $this->belongsTo(CorreoNotificacion::class, 'IdCorreo', 'IdCorreo');
    }

    public function tipoProceso()
    {
         // Asegúrate que TipoProceso use 'IdTipoProceso' como primaryKey si no es 'id'
        return $this->belongsTo(TipoProceso::class, 'IdTipoProceso', 'IdTipoProceso');
    }

    /**
     * ¡FUNCIÓN ACTUALIZADA!
     * Obtiene un array asociativo con listas de emails ('to' y 'cc')
     * para una clave de proceso específica.
     */
    public static function getRecipientsByProcess(string $claveProceso): array
    {
        $recipients = ['to' => [], 'cc' => []]; // Array para almacenar los resultados

        // 1. Encontrar el Id del Proceso usando la Clave
        $proceso = TipoProceso::where('Clave', $claveProceso)->first();

        if (!$proceso) {
            \Log::warning("Se buscó una lista de correos para una clave de proceso inexistente: {$claveProceso}");
            return $recipients; // Devuelve arrays vacíos
        }

        // 2. Obtener las asignaciones CON el tipo y el correo asociado (activo)
        $asignaciones = self::with('correo') // Carga la relación 'correo'
                            ->where('IdTipoProceso', $proceso->IdTipoProceso)
                            // Filtrar directamente aquí para asegurar que el correo exista y esté activo
                            ->whereHas('correo', function ($query) {
                                $query->where('Activo', true);
                            })
                            ->get();

        // 3. Clasificar los correos en 'to' y 'cc'
        foreach ($asignaciones as $asignacion) {
            // Verificamos de nuevo que la relación y el correo existen
            if ($asignacion->correo && $asignacion->correo->Correo) {
                if ($asignacion->TipoDestinatario === 'cc') {
                    $recipients['cc'][] = $asignacion->correo->Correo;
                } else {
                    // Por defecto, o si es explícitamente 'to', va a la lista principal
                    $recipients['to'][] = $asignacion->correo->Correo;
                }
            }
        }

        // 4. Asegurar que no haya duplicados en cada lista
        $recipients['to'] = array_unique($recipients['to']);
        $recipients['cc'] = array_unique($recipients['cc']);

        return $recipients;
    }
}