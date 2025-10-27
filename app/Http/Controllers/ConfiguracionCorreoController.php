<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorreoNotificacion;
use App\Models\TipoProceso;
use App\Models\ProcesoCorreo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Importante para logging
use Illuminate\Support\Facades\Validator; // Asegúrate de importar Validator si lo usas explícitamente

class ConfiguracionCorreoController extends Controller
{
    /**
     * Muestra la página principal de administración.
     * Carga los correos (todos y activos), los procesos y los conteos de asignaciones.
     * MODIFICADO para pasar conteos y separar correos.
     */
    public function index()
    {
        try {
            // Correos activos (para el modal y potencialmente la tabla original si la mantienes)
            $correosActivos = CorreoNotificacion::where('Activo', true)->orderBy('NombreDestinatario')->get();
            // Todos los correos (para el catálogo)
            $correosCatalogo = CorreoNotificacion::orderBy('NombreDestinatario')->get();
            // Todos los procesos
            $procesos = TipoProceso::orderBy('Nombre')->get();

            // Creamos un mapa de asignaciones Y CONTEOS: [idProceso] => ['to_count' => N, 'cc_count' => N]
            $asignaciones = [];
            // Optimización: Obtener solo los datos necesarios y agrupar en PHP
            // Unimos con correos para asegurarnos de contar solo los activos
            $asignacionesData = ProcesoCorreo::join('cat_correos_notificacion', 'proceso_correo.IdCorreo', '=', 'cat_correos_notificacion.IdCorreo')
                                             ->where('cat_correos_notificacion.Activo', true) // Solo contar correos activos
                                             ->select('IdTipoProceso', 'TipoDestinatario', DB::raw('count(*) as total'))
                                             ->groupBy('IdTipoProceso', 'TipoDestinatario')
                                             ->get();

            // Inicializar conteos para todos los procesos
            foreach ($procesos as $proceso) {
                 $asignaciones[$proceso->IdTipoProceso] = ['to_count' => 0, 'cc_count' => 0];
            }
            // Llenar con los conteos reales obtenidos de la BD
            foreach ($asignacionesData as $item) {
                // Comprobar si el IdTipoProceso existe en nuestro array inicializado (seguridad)
                if (isset($asignaciones[$item->IdTipoProceso])) {
                    if ($item->TipoDestinatario === 'cc') {
                        $asignaciones[$item->IdTipoProceso]['cc_count'] = $item->total;
                    } else { // Asumimos 'to'
                        $asignaciones[$item->IdTipoProceso]['to_count'] = $item->total;
                    }
                }
            }

            // Pasamos las variables necesarias a la vista
            return view('configuracion-correos.index', [
                'correosCatalogo' => $correosCatalogo, // Para la lista del catálogo
                'correos' => $correosActivos,         // Para el modal (solo activos)
                'procesos' => $procesos,
                'asignaciones' => $asignaciones        // Contiene los conteos to/cc por proceso
            ]);

        } catch (\Exception $e) {
            // Registrar error detallado
            Log::error("Error cargando la página de configuración de correos: " . $e->getMessage());
            // Redirigir con un mensaje genérico para el usuario
            return redirect()->route('dashboard')->with('error', 'No se pudo cargar la página de configuración de correos. Intente más tarde.');
        }
    }

    /**
     * Guarda un nuevo correo en el catálogo.
     */
    public function storeCorreo(Request $request)
    {
         // Usar Validator explícito para mejor control de errores
         $validator = Validator::make($request->all(), [
            'Correo' => [
                'required',
                'email',
                'max:100',
                // Validación unique insensible a mayúsculas/minúsculas
                Rule::unique('cat_correos_notificacion', 'Correo')->where(function ($query) use ($request) {
                     // Ajusta 'LOWER' según tu motor de BD si es necesario (PostgreSQL/MySQL)
                     // return $query->whereRaw('LOWER(Correo) = ?', [strtolower($request->Correo)]);
                     // Para SQL Server podría ser:
                     // return $query->whereRaw('LOWER(Correo) = LOWER(?)', [$request->Correo]);
                     // O si la colación es insensible, simplemente:
                     return $query->where('Correo', $request->Correo);
                })
            ],
            'NombreDestinatario' => 'required|string|max:150',
        ], [
            'Correo.unique' => 'El correo electrónico ingresado ya existe en el catálogo.', // Mensaje personalizado
        ]);

         // Redirigir con errores si falla la validación
         if ($validator->fails()) {
            return redirect()->route('config.correos.index')
                             ->withErrors($validator)
                             ->withInput(); // Mantener los datos en el formulario
        }

        try {
            // Crear el nuevo registro
            CorreoNotificacion::create([
                'Correo' => $request->Correo,
                'NombreDestinatario' => $request->NombreDestinatario,
                'Activo' => true, // Siempre activo al crearse
            ]);
            // Redirigir con mensaje de éxito
            return redirect()->route('config.correos.index')->with('success', 'Correo registrado exitosamente.');
        } catch (\Exception $e) {
            // Registrar error detallado
            Log::error("Error al guardar correo: " . $e->getMessage());
            // Redirigir con mensaje de error genérico para el usuario
             return redirect()->route('config.correos.index')
                             ->withErrors(['general' => 'Ocurrió un error al guardar el correo. Verifique los datos e intente de nuevo.'])
                             ->withInput();
        }
    }

    /**
     * Obtiene los correos activos y las asignaciones para un proceso específico (para el modal).
     * Devuelve JSON para ser consumido por AJAX/Fetch.
     */
    public function getAsignacionesPorProceso($idProceso)
    {
        try {
            // Validar que el proceso exista (usando findOrFail para lanzar excepción si no existe)
            TipoProceso::findOrFail($idProceso);

            // 1. Obtener todos los correos ACTIVOS
            $correosActivos = CorreoNotificacion::where('Activo', true)
                                                ->orderBy('NombreDestinatario')
                                                ->get(['IdCorreo', 'NombreDestinatario', 'Correo']); // Columnas necesarias

            // 2. Obtener las asignaciones ACTUALES ('to'/'cc') para ESTE proceso
            //    Asegurándose que el correo asociado también esté activo (doble chequeo)
            $asignacionesActuales = ProcesoCorreo::where('IdTipoProceso', $idProceso)
                                                ->whereHas('correo', fn($q) => $q->where('Activo', true))
                                                ->pluck('TipoDestinatario', 'IdCorreo') // Formato: [IdCorreo => 'to'/'cc']
                                                ->toArray();

            // Devolver respuesta JSON exitosa
            return response()->json([
                'correos' => $correosActivos,
                'asignaciones' => $asignacionesActuales
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             Log::warning("Intento de obtener asignaciones para proceso inexistente ID: {$idProceso}");
             return response()->json(['error' => 'El proceso especificado no fue encontrado.'], 404);
        } catch (\Exception $e) {
            // Registrar error detallado
            Log::error("Error al obtener asignaciones AJAX para proceso {$idProceso}: " . $e->getMessage());
            // Devolver error genérico al cliente
            return response()->json(['error' => 'Error interno del servidor al cargar los datos. Por favor, intente más tarde.'], 500);
        }
    }


    /**
     * Guarda las asignaciones enviadas desde el modal para UN proceso específico.
     */
    public function storeAsignacionesPorProceso(Request $request)
    {
        // Validación de los datos que vienen del formulario del modal
        $validator = Validator::make($request->all(), [
            'IdTipoProceso' => 'required|integer|exists:cat_tipos_proceso,IdTipoProceso',
            'modal_asignaciones' => 'nullable|array', // Puede venir vacío si se desmarcan todos
            // Validar cada elemento dentro del array 'modal_asignaciones'
            // La clave debe ser un entero (ID del correo) y el valor uno de los permitidos
            'modal_asignaciones.*' => 'required|in:to,cc,none',
            'modal_asignaciones' => function ($attribute, $value, $fail) {
                foreach (array_keys($value) as $key) {
                    if (!is_numeric($key) || intval($key) <= 0) {
                        $fail("Las claves de las asignaciones deben ser IDs de correo válidos.");
                        break;
                    }
                }
            },
        ]);

        // Si la validación falla, redirigir con error
        if ($validator->fails()) {
            Log::warning("Validación fallida al guardar asignaciones por proceso.", ['errors' => $validator->errors()->toArray(), 'input' => $request->all()]);
            return redirect()->route('config.correos.index')
                             ->with('error_modal', 'Los datos enviados son inválidos. Por favor, revisa la información.');
                             // Opcional: ->withErrors($validator, 'modal') si quieres manejar errores más específicos en la vista
        }

        $idProceso = $request->input('IdTipoProceso');
        $asignacionesInput = $request->input('modal_asignaciones', []); // Default a array vacío

        try {
            DB::transaction(function () use ($idProceso, $asignacionesInput) {
                // 1. Borrar asignaciones ANTERIORES solo para ESTE proceso específico
                ProcesoCorreo::where('IdTipoProceso', $idProceso)->delete();

                // 2. Volver a insertar solo las marcadas como 'to' o 'cc' para ESTE proceso
                foreach ($asignacionesInput as $idCorreo => $tipoDestinatario) {
                    // Solo insertar si el tipo es 'to' o 'cc'
                    if (in_array($tipoDestinatario, ['to', 'cc'])) {
                         // Doble verificación: Asegurar que el correo realmente exista y esté activo antes de asignar
                        $correo = CorreoNotificacion::find($idCorreo);
                        if($correo && $correo->Activo) {
                            ProcesoCorreo::create([
                                'IdTipoProceso' => $idProceso,
                                'IdCorreo' => $idCorreo,
                                'TipoDestinatario' => $tipoDestinatario,
                            ]);
                        } else {
                             // Registrar si se intentó asignar un correo inválido/inactivo
                             Log::warning("Intento de asignar correo inactivo o inexistente (ID: {$idCorreo}) al proceso ID: {$idProceso}. Asignación omitida.");
                        }
                    }
                    // Si el valor es 'none', simplemente no se hace nada (ya se borró antes)
                }
            }); // Fin transacción

            // Obtener nombre del proceso para el mensaje de éxito
            $proceso = TipoProceso::find($idProceso); // Ya validamos que existe
            $nombreProceso = $proceso->Nombre ?? "ID {$idProceso}";

            // Redirigir con mensaje de éxito
            return redirect()->route('config.correos.index')
                             ->with('success', "Asignaciones para el proceso '{$nombreProceso}' actualizadas correctamente.");

        } catch (\Exception $e) {
            // Registrar error detallado
            Log::error("Error crítico al guardar asignaciones para proceso {$idProceso}: " . $e->getMessage());
            // Redirigir con mensaje de error genérico para el usuario
            return redirect()->route('config.correos.index')
                             ->with('error_modal', 'Ocurrió un error grave al guardar las asignaciones. Por favor, contacta al administrador.');
        }
    }


    /**
     * Cambia el estado Activo/Inactivo de un correo.
     * Se usa método DELETE por convención RESTful, aunque sea una actualización.
     */
    public function destroyCorreo($id)
    {
        try {
            // Usar findOrFail para manejar automáticamente el caso de ID no encontrado
            $correo = CorreoNotificacion::findOrFail($id);

            // Invertir el estado actual
            $nuevoEstado = !$correo->Activo;
            $correo->update(['Activo' => $nuevoEstado]);

            $accion = $nuevoEstado ? 'activado' : 'desactivado';
            $mensaje = "Correo '{$correo->Correo}' {$accion} correctamente.";

            // Mensajes adicionales según la acción
            if (!$nuevoEstado) {
                $mensaje .= " Ya no aparecerá para nuevas asignaciones ni recibirá correos configurados.";
                // Consideración: ¿Eliminar asignaciones existentes al desactivar?
                // Si decides hacerlo, descomenta la siguiente línea:
                // ProcesoCorreo::where('IdCorreo', $id)->delete();
                // $mensaje .= ' Sus asignaciones existentes también fueron eliminadas.';
            } else {
                 $mensaje .= " Volverá a estar disponible para asignaciones.";
            }

            // Redirigir con mensaje de éxito
            return redirect()->route('config.correos.index')->with('success', $mensaje);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             // Registrar advertencia si se intenta modificar un correo inexistente
             Log::warning("Intento de activar/desactivar correo no encontrado ID: {$id}");
             // Devolver error para el usuario
             return redirect()->route('config.correos.index')->withErrors(['general' => 'El correo que intentas modificar no fue encontrado.']);
        } catch (\Exception $e) {
            // Registrar error detallado
            Log::error("Error al cambiar estado del correo ID {$id}: " . $e->getMessage());
            // Devolver error genérico para el usuario
            return redirect()->route('config.correos.index')->withErrors(['general' => 'Ocurrió un error inesperado al cambiar el estado del correo.']);
        }
    }

} // Fin ConfiguracionCorreoController