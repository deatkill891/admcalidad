<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperAnalisisHorno; // Importa el modelo
use App\Models\CatTecnicoHorno; // Importa el modelo
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\ProcesoCorreo; // <--- ¡AÑADIDO!
use App\Mail\AnalisisCompletoMail; // <--- ¡AÑADIDO! (Asumiendo que lo usarás)
use Illuminate\Support\Facades\Mail; // <--- ¡AÑADIDO!

class AnalisisHornoController extends Controller
{
    /**
     * Mapeo de los tipos de análisis para la lógica del controlador.
     */
    private $tiposAnalisis = [
        'hf' => ['id' => 1, 'nombre' => 'Fusión (HF)', 'apc' => 1, 'claveProceso' => 'HORNO_HF'], // Añadida claveProceso
        'ha' => ['id' => 2, 'nombre' => 'Afino (HA)', 'apc' => 2, 'claveProceso' => 'HORNO_HA'], // Añadida claveProceso
        'mcc' => ['id' => 3, 'nombre' => 'Colada (MCC)', 'apc' => 3, 'claveProceso' => 'HORNO_MCC'], // Añadida claveProceso
    ];

    /**
     * Muestra el formulario y la tabla de registros.
     */
    public function create(string $tipo)
    {
        if (!array_key_exists($tipo, $this->tiposAnalisis)) {
            abort(404, 'Tipo de análisis no válido.');
        }

        $infoTipo = $this->tiposAnalisis[$tipo];

        // 1. Datos para el Formulario
        $tecnicos = CatTecnicoHorno::where('Apc', $infoTipo['apc'])
                                        ->orderBy('NomTecnico')
                                        ->get();

        // 2. Datos para la Tabla
        $analisisRegistrados = OperAnalisisHorno::where('IdTipoAnalisis', $infoTipo['id'])
                                        ->where('IdEstatusAnalisis', '!=', 5) 
                                        ->orderBy('Fecha', 'desc') 
                                        ->get(); 
        
        return view('analisis-horno.create', [
            'tipo' => $tipo, 
            'titulo' => $infoTipo['nombre'],
            'tecnicos' => $tecnicos,
            'analisisRegistrados' => $analisisRegistrados, 
        ]);
    }

    /**
     * Almacena el nuevo análisis en la base de datos.
     */
    public function store(Request $request)
    {
        $tipo = $request->input('tipo');

        if (!array_key_exists($tipo, $this->tiposAnalisis)) {
            return back()->withInput()->withErrors(['tipo' => 'Tipo de análisis no válido.']);
        }

        $infoTipo = $this->tiposAnalisis[$tipo];
        $idTipoAnalisis = $infoTipo['id'];

        // --- Validación ---
        $reglasComunes = [
            'tipo' => 'required|in:hf,ha,mcc',
            'Fecha' => 'required|date',
            'Tecnico' => 'required|string|max:255',
            'HORNO' => 'nullable|string|max:255',
            'Turno' => 'nullable|string|max:255',
            'COLADA' => 'nullable|string|max:255',
            'GRADO' => 'nullable|string|max:255',
            'CaO' => 'nullable|numeric|min:0',
            'MgO' => 'nullable|numeric|min:0',
            'SiO2' => 'nullable|numeric|min:0',
            'Al2O3' => 'nullable|numeric|min:0',
            'MnO' => 'nullable|numeric|min:0',
            'FeO' => 'nullable|numeric|min:0',
            'S' => 'nullable|numeric|min:0',
            'IB3' => 'nullable|numeric',
            'IB4' => 'nullable|numeric',
            'TOTAL' => 'nullable|numeric',
        ];

        $reglasEspecificas = [];
        if ($tipo === 'hf') {
            $reglasEspecificas = [
                'KgCalSiderurgica' => 'nullable|numeric|min:0',
                'KgCalDolomitica' => 'nullable|numeric|min:0',
            ];
        } else { // para 'ha' y 'mcc'
            $reglasEspecificas = [
                'IB2' => 'nullable|numeric',
            ];
        }

        $request->validate(array_merge($reglasComunes, $reglasEspecificas));

        // --- Guardado en BD ---
        try {
            // Guardamos la instancia creada para usarla después
            $registro = OperAnalisisHorno::create([
                'Fecha' => Carbon::parse($request->input('Fecha')),
                'Tecnico' => $request->input('Tecnico'),
                'HORNO' => $request->input('HORNO'),
                'Turno' => $request->input('Turno'),
                'COLADA' => $request->input('COLADA'),
                'GRADO' => $request->input('GRADO'),
                
                'CaO' => $request->input('CaO'),
                'MgO' => $request->input('MgO'),
                'SiO2' => $request->input('SiO2'),
                'Al2O3' => $request->input('Al2O3'),
                'MnO' => $request->input('MnO'),
                'FeO' => $request->input('FeO'),
                'S' => $request->input('S'),

                'IB2' => $request->input('IB2') ?? null, 
                'IB3' => $request->input('IB3'),
                'IB4' => $request->input('IB4'),
                'TOTAL' => $request->input('TOTAL'),
                'KgCalSiderurgica' => $request->input('KgCalSiderurgica') ?? null, 
                'KgCalDolomitica' => $request->input('KgCalDolomitica') ?? null, 
                
                'IdUsuario' => Auth::id(),
                'NombreUsuario' => Auth::user()->username, // Asegúrate que tu modelo Usuario tenga 'username'
                'IdTipoAnalisis' => $idTipoAnalisis,
                'TipoMuestra' => $infoTipo['nombre'],
                'IdEstatusAnalisis' => 2, // 2 = Registrado/Completado
            ]);

            // --- LÓGICA DE ENVÍO DE CORREO (¡MODIFICADA!) ---
            try {
                // Recargamos el registro con las relaciones necesarias para el correo
                $registroCompleto = OperAnalisisHorno::with(['usuario']) // Añade más relaciones si tu Mailable las necesita
                                                    ->find($registro->IdOperAnalisisHorno); // Usamos el ID del registro recién creado

                // 1. Obtener la CLAVE del proceso desde nuestro array $infoTipo
                $claveProceso = $infoTipo['claveProceso'] ?? null;
                
                // 2. Obtener la lista dinámica de correos desde la BD
                $listaEmails = [];
                if ($claveProceso) {
                    $listaEmails = ProcesoCorreo::getRecipientsByProcess($claveProceso);
                } else {
                     \Log::warning("No se encontró clave de proceso para el tipo: {$tipo} en AnalisisHornoController@store");
                }

                // 3. (Opcional) Añadir correo del operador que registró
                if ($registroCompleto->usuario && $registroCompleto->usuario->email) {
                    $listaEmails[] = $registroCompleto->usuario->email;
                }

                // 4. Asegurarse de que no haya duplicados y filtrar nulos/vacíos
                $listaEmails = array_unique(array_filter($listaEmails));
                
                if (count($listaEmails) > 0) {
                    // ¡IMPORTANTE! Asumo que tu Mailable AnalisisCompletoMail puede manejar
                    // tanto objetos Muestra como objetos OperAnalisisHorno.
                    // Si no es así, necesitarás crear un Mailable específico para Análisis de Horno
                    // o adaptar el existente.
                    // También, el segundo parámetro ($reglas_material) no existe aquí, puedes pasar null o un array vacío.
                    Mail::to($listaEmails)->send(new AnalisisCompletoMail($registroCompleto, [])); // Pasar array vacío como segundo parámetro
                } else {
                     \Log::info("No se enviaron correos para el proceso {$claveProceso} (Registro ID: {$registro->IdOperAnalisisHorno}). Lista de destinatarios vacía.");
                }

            } catch (\Exception $e) {
                 \Log::error("Error al enviar correo de notificación de análisis de horno (Registro ID: {$registro->IdOperAnalisisHorno}): " . $e->getMessage());
                // No detenemos el flujo principal, solo registramos el error de correo
                // Podrías añadir un mensaje flash secundario si lo deseas.
                 return redirect()->route('analisis-horno.create', ['tipo' => $tipo])
                                ->with('success', 'Análisis registrado, pero hubo un error al enviar la notificación por correo.');
            }
             // --- FIN DE LA MODIFICACIÓN ---

            // Redirigir de vuelta a la misma página
            return redirect()->route('analisis-horno.create', ['tipo' => $tipo])
                             ->with('success', 'Análisis de ' . $infoTipo['nombre'] . ' registrado y notificado exitosamente.');

        } catch (\Exception $e) {
             \Log::error("Error al guardar análisis de horno ({$tipo}): " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar el análisis.');
        }
    }
    
    /**
     * Realiza un "soft delete" del registro cambiando el estatus a 5.
     */
    public function destroy(OperAnalisisHorno $registro)
    {
        if (!Auth::user() || !Auth::user()->permiso || Auth::user()->permiso->Administrador != 1) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        try {
            $registro->IdEstatusAnalisis = 5;
            $registro->save(); 

            return back()->with('success', 'Registro #' . $registro->IdOperAnalisisHorno . ' eliminado correctamente.'); // Corregido: Usar IdOperAnalisisHorno

        } catch (\Exception $e) {
             \Log::error("Error al eliminar registro de análisis de horno (ID: {$registro->IdOperAnalisisHorno}): " . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el registro.');
        }
    }
}