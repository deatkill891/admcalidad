<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OperAnalisisHorno;
use App\Models\CatTecnicoHorno;
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
     * Muestra el formulario dinámico para crear un nuevo análisis.
     */
    public function create(string $tipo)
    {
        if (!array_key_exists($tipo, $this->tiposAnalisis)) {
            abort(404, 'Tipo de análisis no válido.');
        }

        $infoTipo = $this->tiposAnalisis[$tipo];

        // Cargar técnicos filtrados por el 'Apc' correspondiente
        $tecnicos = CatTecnicoHorno::where('Apc', $infoTipo['apc'])
                                    ->orderBy('NomTecnico')
                                    ->get();

        // Cargar otros catálogos si los necesitas (ej. Grados, Hornos)
        // $grados = CatGrados::all(); 

        return view('analisis-horno.create', [
            'tipo' => $tipo, // 'hf', 'ha', o 'mcc'
            'titulo' => $infoTipo['nombre'],
            'tecnicos' => $tecnicos,
            // 'grados' => $grados,
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
                'Fecha' => Carbon::parse($request->input('Fecha')), // Asegurar formato
                'Tecnico' => $request->input('Tecnico'),
                'HORNO' => $request->input('HORNO'),
                'Turno' => $request->input('Turno'),
                'COLADA' => $request->input('COLADA'),
                'GRADO' => $request->input('GRADO'),
                
                // Campos de elementos
                'CaO' => $request->input('CaO'),
                'MgO' => $request->input('MgO'),
                'SiO2' => $request->input('SiO2'),
                'Al2O3' => $request->input('Al2O3'),
                'MnO' => $request->input('MnO'),
                'FeO' => $request->input('FeO'),
                'S' => $request->input('S'),

                // Campos calculados y específicos
                'IB2' => $request->input('IB2') ?? null, // Solo vendrá de HA y MCC
                'IB3' => $request->input('IB3'),
                'IB4' => $request->input('IB4'),
                'TOTAL' => $request->input('TOTAL'),
                'KgCalSiderurgica' => $request->input('KgCalSiderurgica') ?? null, // Solo vendrá de HF
                'KgCalDolomitica' => $request->input('KgCalDolomitica') ?? null, // Solo vendrá de HF
                
                // Datos del sistema
                'IdUsuario' => Auth::id(),
                'NombreUsuario' => Auth::user()->username, // O Auth::user()->name
                'IdTipoAnalisis' => $idTipoAnalisis,
                'TipoMuestra' => $infoTipo['nombre'],
                'IdEstatusAnalisis' => 2, // 2 = 'Completado' o 'Registrado'. Ajusta si es necesario.
            ]);

            // Redirige de vuelta al dashboard (o a una lista de análisis)
            return redirect()->route('dashboard')->with('success', 'Análisis de ' . $infoTipo['nombre'] . ' registrado exitosamente.');

        } catch (\Exception $e) {
            // Log::error("Error al guardar análisis horno: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al guardar: ' . $e->getMessage());
        }
    }
}