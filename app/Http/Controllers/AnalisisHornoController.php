<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperAnalisisHorno; // Importa el modelo
use App\Models\CatTecnicoHorno; // Importa el modelo
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnalisisHornoController extends Controller
{
    /**
     * Mapeo de los tipos de análisis para la lógica del controlador.
     */
    private $tiposAnalisis = [
        'hf' => ['id' => 1, 'nombre' => 'Fusión (HF)', 'apc' => 1],
        'ha' => ['id' => 2, 'nombre' => 'Afino (HA)', 'apc' => 2],
        'mcc' => ['id' => 3, 'nombre' => 'Colada (MCC)', 'apc' => 3],
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

        // 2. Datos para la Tabla (basado en los apc-table-*.php)
        // Buscamos registros con el IdTipoAnalisis correspondiente Y que no estén eliminados (IdEstatusAnalisis != 5)
        $analisisRegistrados = OperAnalisisHorno::where('IdTipoAnalisis', $infoTipo['id'])
                                    ->where('IdEstatusAnalisis', '!=', 5) 
                                    ->orderBy('Fecha', 'desc') // Ordenar por fecha descendente
                                    ->get(); 
        
        return view('analisis-horno.create', [
            'tipo' => $tipo, // 'hf', 'ha', o 'mcc'
            'titulo' => $infoTipo['nombre'],
            'tecnicos' => $tecnicos,
            'analisisRegistrados' => $analisisRegistrados, // <-- ¡NUEVA VARIABLE!
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
            OperAnalisisHorno::create([
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

            // Redirigir de vuelta a la misma página (para ver el nuevo registro en la tabla)
            return redirect()->route('analisis-horno.create', ['tipo' => $tipo])
                             ->with('success', 'Análisis de ' . $infoTipo['nombre'] . ' registrado exitosamente.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }
    
    // <-- INICIO: MÉTODO AÑADIDO PARA "ELIMINAR" (SOFT DELETE) -->
    /**
     * Realiza un "soft delete" del registro cambiando el estatus a 5.
     *
     * @param \App\Models\OperAnalisisHorno $registro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(OperAnalisisHorno $registro)
    {
        // 1. Validar que el usuario tenga permiso de Administrador
        if (!Auth::user() || !Auth::user()->permiso || Auth::user()->permiso->Administrador != 1) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        try {
            // 2. Actualizar el estatus a 5 (Eliminado)
            $registro->IdEstatusAnalisis = 5;
            $registro->save(); // Guardar el cambio

            // 3. Redirigir de vuelta con un mensaje de éxito
            return back()->with('success', 'Registro #' . $registro->IdRegistro . ' eliminado correctamente.');

        } catch (\Exception $e) {
            // Manejo de errores
            // \Log::error("Error al eliminar registro: " . $e->getMessage()); // Opcional: registrar el error
            return back()->with('error', 'No se pudo eliminar el registro.');
        }
    }
    // <-- FIN: MÉTODO AÑADIDO -->
}