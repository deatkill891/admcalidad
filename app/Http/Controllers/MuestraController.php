<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material;
use App\Models\Elemento;
use App\Models\ResultadoAnalisis;
use App\Mail\AnalisisCompletoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\ProcesoCorreo;

class MuestraController extends Controller
{
    /**
     * Esta función privada (la original) define los LÍMITES DE VALIDACIÓN (Cumple/No Cumple)
     * para la BD y el correo.
     */
    private function obtenerCriterios($id_material, $id_elemento = null)
    {
        // Array de criterios copiado de lab-insert-analisis.php
    $criterios = [
        16 => [
            28 => ['min' => 92, 'max' => 100], 
            29 => ['min' => 0, 'max' => 3], 
            7  => ['min' => 0, 'max' => 2.5], 
            1 => ['min' => 0, 'max' => 1], 
            30 => ['min' => 0, 'max' => 4], 
        ],
        3 => [
            1 => ['min' => 0, 'max' => 1], 
        ],
        2 => [
            1 => ['min' => 0, 'max' => 1], 
        ],
        18 => [
            28 => ['min' => 92, 'max' => 100], 
            29 => ['min' => 0, 'max' => 3], 
            7  => ['min' => 0, 'max' => 2.5], 
            30 => ['min' => 0, 'max' => 4], 
            1 => ['min' => 0, 'max' => 1], 
        ],        
        17 => [
            28 => ['min' => 96, 'max' => 100], 
            29 => ['min' => 0, 'max' => 3], 
            7  => ['min' => 0, 'max' => 2.5], 
            30 => ['min' => 0, 'max' => 3], 
            1 => ['min' => 0, 'max' => 1],
            40 => ['min' => 0, 'max' => 850], 
        ],        
        14 => [
            25 => ['min' => 75, 'max' => 100], 
            26 => ['min' => 0, 'max' => 8], 
            27 => ['min' => 0, 'max' => 10], 
            7 => ['min' => 0, 'max' => 0.5], 
            1 => ['min' => 0, 'max' => 1], 
        ],      
        10 => [
            13 => ['min' => 75, 'max' => 100], 
            11 => ['min' => 0, 'max' => 1.5], 
            16 => ['min' => 0, 'max' => 0.10], 
            1 => ['min' => 0, 'max' => 0.5], 
        ],          
        19 => [
            20 => ['min' => 64, 'max' => 100], 
            13 => ['min' => 14, 'max' => 100], 
            16 => ['min' => 0, 'max' => 2.5], 
            7 => ['min' => 0, 'max' => 0.04],
            1 => ['min' => 0, 'max' => 0.5], 
        ],     
        4 => [
            13 => ['min' => 60, 'max' => 100], 
            16 => ['min' => 30, 'max' => 100], 
            41 => ['min' => 0, 'max' => 3], 
            26 => ['min' => 0, 'max' => 2],
            1 => ['min' => 0, 'max' => 0.5], 
        ],           
        5 => [
            17 => ['min' => 60, 'max' => 100], 
            16 => ['min' => 0, 'max' => 8.5], 
            13 => ['min' => 3.5, 'max' => 100], 
            18 => ['min' => 0, 'max' => 0.04], 
            7 => ['min' => 0, 'max' => 0.04],
            1 => ['min' => 0, 'max' => 1], 
        ],
        15 => [
            28 => ['min' => 85, 'max' => 100], 
            29 => ['min' => 0, 'max' => 5], 
            7 => ['min' => 0, 'max' => 2.5], 
            1 => ['min' => 0, 'max' => 1], 
            30 => ['min' => 0, 'max' => 10]
        ],
        1 => [
            11 => ['min' => 96, 'max' => 100], 
            4 => ['min' => 0, 'max' => 1.5], 
            14 => ['min' => 0, 'max' => 0.3], 
            2 => ['min' => 0, 'max' => 0.5], 
            13 => ['min' => 0, 'max' => 1]
        ],
        9 => [
            21 => ['min' => 63, 'max' => 100], 
            11 => ['min' => 0, 'max' => 2], 
            13 => ['min' => 0, 'max' => 2], 
            16 => ['min' => 0, 'max' => 0.2], 
            7 => ['min' => 0, 'max' => 0.1],
            22 => ['min' => 0, 'max' => 0.2], 
            4 => ['min' => 0, 'max' => 30], 
            1 => ['min' => 0, 'max' => 1]            
        ] ,
        12 => [
            23 => ['min' => 68, 'max' => 100], 
            11 => ['min' => 0, 'max' => 5], 
            13  => ['min' => 0, 'max' => 30], 
            18 => ['min' => 0, 'max' => 0.5],      
        ],
        43 => [
            10 => ['min' => 0, 'max' => 0.575], 
            17 => ['min' => 0, 'max' => 0.575], 
            35 => ['min' => 0, 'max' => 0.475], 
            36 => ['min' => 0, 'max' => 0.025], 
            37 => ['min' => 0, 'max' => 0.060],      
        ],       
        44 => [
            10 => ['min' => 0, 'max' => 0.060], 
            17 => ['min' => 0, 'max' => 0.050], 
            35 => ['min' => 0, 'max' => 0.080], 
            36 => ['min' => 0, 'max' => 0.015], 
            37 => ['min' => 0, 'max' => 0.015],      
        ],             
        45 => [
            10 => ['min' => 0, 'max' => 0.175], 
            17 => ['min' => 0, 'max' => 0.120], 
            35 => ['min' => 0, 'max' => 0.080], 
            36 => ['min' => 0, 'max' => 0.015], 
            37 => ['min' => 0, 'max' => 0.015],      
        ],                    
        46 => [
            10 => ['min' => 0, 'max' => 0.12], 
            17 => ['min' => 0, 'max' => 0.12], 
            35 => ['min' => 0, 'max' => 0.080], 
            36 => ['min' => 0, 'max' => 0.020], 
            37 => ['min' => 0, 'max' => 0.015],      
        ],
        47 => [
            10 => ['min' => 0, 'max' => 0.350], 
            17 => ['min' => 0, 'max' => 0.150], 
            35 => ['min' => 0, 'max' => 0.150], 
            36 => ['min' => 0, 'max' => 0.030], 
            37 => ['min' => 0, 'max' => 0.030],      
        ], 
        48 => [
            10 => ['min' => 0, 'max' => 0.175], 
            17 => ['min' => 0, 'max' => 0.175], 
            35 => ['min' => 0, 'max' => 0.080], 
            36 => ['min' => 0, 'max' => 0.015], 
            37 => ['min' => 0, 'max' => 0.015],      
        ],  
        49 => [
            16 => ['min' => 3.5, 'max' => 4.5], 
            20 => ['min' => 0, 'max' => 0.1], 
            13 => ['min' => 0, 'max' => 0.8], 
            18 => ['min' => 0, 'max' => 0.08], 
            7 => ['min' => 0, 'max' => 0.020],      
        ],
            20 => [ // FERROMANGANESO (y Polvo de Zinc según tu lógica)
                // Ferromanganeso
                12 => ['min' => 0, 'max' => 2], 
                13 => ['min' => 78, 'max' => 100], 
                // Polvo de Zinc (elementos de tu snippet)
                // (Asumiendo que 24=Zn, 25=Pb, 26=Fe, 27=Cd, 31=Cu)
                24 => ['min' => 0, 'max' => 100], // Zn
                25 => ['min' => 0, 'max' => 100], // Pb
                26 => ['min' => 0, 'max' => 100], // Fe
                27 => ['min' => 0, 'max' => 100], // Cd
                31 => ['min' => 0, 'max' => 100], // Cu
            ]
        ];
        if (isset($criterios[$id_material])) {
            $reglas_material = $criterios[$id_material];
            if ($id_elemento !== null) {
                // Asegurarse de que el id_elemento exista como clave antes de devolver
                return isset($reglas_material[$id_elemento]) ? $reglas_material[$id_elemento] : null;
            }
            return $reglas_material;
        }
        return null;
    }


    /**
     * Define qué campos (elementos) se MUESTRAN en el formulario.
     */
    private function obtenerCamposPorMaterial($id_material)
    {
        // Array exacto que proporcionaste
        $campos = [
            '1' => [11, 4, 12, 2, 13],
            '2' => [1],
            '3' => [1],
            '4' => [13, 16],
            '5' => [17, 16, 13, 18, 7],
            '6' => [19, 16, 11, 13, 18, 7, 1],
            '7' => [19, 16, 11, 13, 18, 7, 1],
            '8' => [20, 16],
            '9' => [21, 11, 13, 16, 7, 22, 4, 1],
            '10' => [13, 11],
            '11' => [13, 11],
            '12' => [23, 11, 13, 18],
            '13' => [24, 11, 13, 16, 7, 18, 1],
            '14' => [25, 26, 27, 1],
            '15' => [48, 29, 28, 7, 30],
            '16' => [28, 29, 7, 30, 1],
            '17' => [32, 48, 30, 40, 7, 29],
            '18' => [32, 29, 7, 30, 1],
            '19' => [20, 13],
            '20' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 16], // Incluye IDs para Zn, Pb, Fe, Cd, Cu si son distintos
            '21' => [2, 3, 4, 7, 8, 10, 12, 13, 16, 17, 18, 20, 23, 24, 36, 37, 38, 39, 40],
            '22' => [41, 42, 43, 6, 43, 44, 14, 45, 16, 7, 46],
            '23' => [10, 17, 35, 36, 37, 39, 34, 18, 20, 47],
            '24' => [16],
            '25' => [14],
            '26' => [25, 27, 53],
            '27' => [20],
            '28' => [16, 7],
            '29' => [50, 51, 52],
            '30' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '32' => [49, 31],
            '33' => [6, 48],
            '34' => [6, 14, 53, 43, 7],
            '35' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '36' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '37' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '38' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '39' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '40' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '41' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '42' => [16, 20, 13, 18, 7, 10, 17, 35, 36, 37, 24, 21, 3, 48, 11],
            '43' => [10, 17, 35, 36, 37, 47],
            '44' => [10, 17, 35, 36, 37],
            '45' => [10, 17, 35, 36, 37],
            '46' => [10, 17, 35, 36, 37],
            '47' => [10, 17, 35, 36, 37],
            '48' => [10, 17, 35, 36, 37],
            '49' => [16, 20, 13, 18, 7],
        ];

        // Usamos (string) para asegurar que coincida con las llaves del array
        return $campos[(string)$id_material] ?? [];
    }


    // --- MÉTODOS DEL CONTROLADOR ---

    public function store(Request $request)
    {
        // ... (sin cambios)
        $request->validate([
            'IdMaterial' => 'required|integer|exists:CatMateriales,IdMaterial',
            'Proveedor' => 'nullable|string|max:255',
            'Remision' => 'nullable|string|max:255',
            // Añadir otras validaciones si son necesarias para los campos dinámicos
        ]);

        try {
            Muestra::create([
                'IdMaterial' => $request->IdMaterial,
                'Proveedor' => $request->Proveedor,
                'Remision' => $request->Remision,
                'FechaRecibo' => now(), // Opcional si no se captura
                'PlacaTractor' => $request->PlacaTractor,
                'PlacaTolva' => $request->PlacaTolva,
                'Tonelaje' => $request->Tonelaje,
                'Solicitante' => $request->Solicitante,
                'Area' => $request->Area,
                'Identificacion' => $request->Identificacion,
                'Analisis' => $request->Analisis,
                'IdEstatusAnalisis' => 1, // 1 = En Espera
                'IdUsuarioOper' => Auth::id(),
                'FechaRegistro' => now(),
            ]);
            return redirect()->route('dashboard')->with('success', '¡Muestra registrada exitosamente!');
        } catch (\Exception $e) {
            Log::error("Error al registrar muestra: " . $e->getMessage());
            return redirect()->route('dashboard')
                             ->withErrors(['general' => 'No se pudo registrar la muestra. Inténtalo de nuevo.'])
                             ->withInput();
        }
    }

    public function analisisIndex()
    {
        // ... (sin cambios)
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1)
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get();
        return view('muestras.analisis', compact('muestrasEnEspera'));
    }

    public function fetchPendientes()
    {
        // ... (sin cambios)
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1)
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get()
                                   ->toArray(); // Convertir a array para mapeo simple

        // Mapeo manual para asegurar la estructura JSON deseada y evitar carga innecesaria
        $muestrasMapeadas = array_map(function($muestra) {
            return [
                'IdMuestra' => $muestra['IdMuestra'],
                'material' => $muestra['material'] ? ['Material' => $muestra['material']['Material']] : null,
                'FechaRegistro' => $muestra['FechaRegistro'],
                'Proveedor' => $muestra['Proveedor'],
                'Solicitante' => $muestra['Solicitante'],
                'usuario_oper' => $muestra['usuario_oper'] ? ['username' => $muestra['usuario_oper']['username']] : null,
            ];
        }, $muestrasEnEspera);

        return response()->json($muestrasMapeadas);
    }

    public function rechazar(Muestra $muestra)
    {
        // ... (sin cambios, considera añadir try-catch)
        if ($muestra) {
            try {
                $muestra->IdEstatusAnalisis = 3; // 3 = Rechazado
                $muestra->save();
                // Opcional: Notificar por correo el rechazo
                return redirect()->route('dashboard')->with('success', 'Muestra ' . $muestra->IdMuestra . ' ha sido rechazada.');
            } catch (\Exception $e) {
                 Log::error("Error al rechazar muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
                 return redirect()->route('dashboard')->with('error', 'No se pudo rechazar la muestra.');
            }
        }
        return redirect()->route('dashboard')->with('error', 'No se pudo encontrar la muestra a rechazar.');
    }

    /**
     * Muestra el formulario de análisis con los campos correctos.
     */
    public function showAnalisisForm(Muestra $muestra)
    {
        // ... (sin cambios, la lógica de cambio de estatus ya está comentada)
        try {
            $muestra->load('material'); // Cargar relación si no viene por Route Model Binding eager loading

            $ids_elementos_a_mostrar = $this->obtenerCamposPorMaterial($muestra->IdMaterial);
            $reglas_validacion = $this->obtenerCriterios($muestra->IdMaterial);
            $elementos_a_analizar = [];

            if (!empty($ids_elementos_a_mostrar) && !is_null($reglas_validacion)) {
                $elementos_db = Elemento::whereIn('IdElemento', $ids_elementos_a_mostrar)
                                        ->get()
                                        ->keyBy('IdElemento');

                foreach ($ids_elementos_a_mostrar as $id) {
                    if (isset($elementos_db[$id])) {
                        // Usamos isset() para verificar si hay regla específica para este elemento
                        $limites = isset($reglas_validacion[$id]) ? $reglas_validacion[$id] : ['min' => 0, 'max' => 100];
                        $elementos_a_analizar[] = [
                            'IdElemento' => $id,
                            'Nombre' => $elementos_db[$id]->Nombre,
                            'ValMin' => $limites['min'] ?? 0, // Default a 0 si no está definido
                            'ValMax' => $limites['max'] ?? 100 // Default a 100 si no está definido
                        ];
                    }
                }
            } else {
                 Log::warning("No se encontraron campos o criterios para el material ID: {$muestra->IdMaterial} en la muestra ID: {$muestra->IdMuestra}");
            }

            // Lógica comentada para no cambiar estatus al abrir
            /*
            if ($muestra->IdEstatusAnalisis == 1) {
                $muestra->IdEstatusAnalisis = 4; // 4 = En Proceso
                $muestra->save();
            }
            */

            $openWeatherApiKey = config('services.openweather.key');

            return view('muestras.analizar-form', compact('muestra', 'elementos_a_analizar', 'openWeatherApiKey'));

        } catch (\Exception $e) {
            Log::error("Error al mostrar formulario de análisis para muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'No se pudo cargar el formulario de análisis.');
        }
    }

    /**
     * Guarda los resultados del análisis en la BD y envía correo.
     * ¡¡MÉTODO CON LÓGICA DE CORREO ACTUALIZADA!!
     */
    public function storeAnalisis(Request $request, Muestra $muestra)
    {
        // --- Validación (SIN CAMBIOS) ---
        $reglas_material = $this->obtenerCriterios($muestra->IdMaterial);
        $rules = [];
        $messages = [];
        $elementos_map = []; // Para lógica Polvo de Zinc

        if ($reglas_material) {
            $ids_elementos_solicitados = array_keys($request->input('resultados', [])); // IDs que vienen del form
            $elementos_db_info = Elemento::whereIn('IdElemento', $ids_elementos_solicitados)->get()->keyBy('IdElemento');

            foreach ($ids_elementos_solicitados as $idElemento) {
                // Solo validar si el elemento tiene criterios definidos
                if (isset($reglas_material[$idElemento])) {
                    $limites = $reglas_material[$idElemento];
                    $nombre = $elementos_db_info[$idElemento]->Nombre ?? "Elemento $idElemento";
                    $min = $limites['min'] ?? 0;
                    $max = $limites['max'] ?? 100;

                    $rules["resultados.{$idElemento}.valor"] = "required|numeric|min:{$min}|max:{$max}";
                    $messages["resultados.{$idElemento}.valor.required"] = "El valor para {$nombre} es requerido.";
                    $messages["resultados.{$idElemento}.valor.numeric"] = "El valor para {$nombre} debe ser numérico.";
                    $messages["resultados.{$idElemento}.valor.min"] = "El valor para {$nombre} debe ser al menos {$min}.";
                    $messages["resultados.{$idElemento}.valor.max"] = "El valor para {$nombre} no debe exceder {$max}.";

                    // Guardar info para lógica Polvo de Zinc si es necesario
                    if(isset($elementos_db_info[$idElemento])) {
                        $elementos_map[$idElemento] = $elementos_db_info[$idElemento];
                    }
                } else if (isset($request->resultados[$idElemento]['valor'])) {
                    // Si se envía un valor pero no hay criterio, al menos validar que sea numérico
                     $nombre = $elementos_db_info[$idElemento]->Nombre ?? "Elemento $idElemento";
                     $rules["resultados.{$idElemento}.valor"] = "required|numeric";
                     $messages["resultados.{$idElemento}.valor.required"] = "El valor para {$nombre} es requerido.";
                     $messages["resultados.{$idElemento}.valor.numeric"] = "El valor para {$nombre} debe ser numérico.";
                }
            }
        }

        $rules['clima'] = 'nullable|string|max:255';
        $rules['humedad'] = 'nullable|string|max:255';

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // --- Guardado de Resultados (SIN CAMBIOS SIGNIFICATIVOS) ---
        try {
            DB::transaction(function () use ($request, $muestra, $elementos_map) {
                // Borrar resultados anteriores por si se re-analiza (opcional)
                // ResultadoAnalisis::where('IdMuestra', $muestra->IdMuestra)->delete();

                foreach ($request->resultados as $idElemento => $resultado) {
                    // Guardar solo si se envió un valor numérico
                    if (isset($resultado['valor']) && is_numeric($resultado['valor'])) {
                        $valor_final = (float) $resultado['valor'];

                        // Lógica Polvo de Zinc (Material ID 20)
                        if ($muestra->IdMaterial == 20 && isset($elementos_map[$idElemento])) {
                            $nombreElemento = $elementos_map[$idElemento]->Nombre;
                            switch ($nombreElemento) {
                                case 'Zn': $valor_final = round($valor_final * 0.8, 3); break;
                                case 'Pb': $valor_final = round($valor_final * 0.93, 3); break;
                                case 'Fe': $valor_final = round($valor_final * 0.7, 3); break;
                                case 'Cd': $valor_final = round($valor_final * 0.88, 3); break;
                                case 'Cu': $valor_final = round($valor_final * 0.8, 3); break;
                                // Añadir más casos si hay otros elementos afectados
                            }
                        }

                        ResultadoAnalisis::create([
                            'IdMuestra' => $muestra->IdMuestra,
                            'IdElemento' => $idElemento,
                            'Valor' => $valor_final,
                            'FechaRegistro' => now(), // Fecha en que se guarda este resultado específico
                        ]);
                    }
                }

                // Actualizar la muestra principal
                $muestra->IdEstatusAnalisis = 2; // 2 = Analizado
                $muestra->IdUsuarioAnal = Auth::id();
                $muestra->FechaAnalisis = now(); // Fecha en que se completa el análisis
                $muestra->Clima = $request->input('clima', null); // Usar null si no viene
                $muestra->Humedad = $request->input('humedad', null);
                $muestra->save();
            }); // Fin de la transacción

        } catch (\Exception $e) {
            Log::error("Error al guardar resultados de análisis para Muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
            return redirect()->back()
                             ->withErrors(['general' => 'Ocurrió un error al guardar los resultados. Inténtalo de nuevo.'])
                             ->withInput();
        }


        // --- LÓGICA DE ENVÍO DE CORREO (ACTUALIZADA con TO y CC) ---
        try {
            // Recargamos la muestra con relaciones necesarias para el correo
            $muestraCompleta = Muestra::with(['material', 'usuarioOper', 'resultados.elemento'])
                                        ->find($muestra->IdMuestra); // Asegúrate que el ID es correcto

            if (!$muestraCompleta) {
                Log::error("No se encontró la Muestra ID {$muestra->IdMuestra} después de guardar para enviar correo.");
                 return redirect()->route('dashboard')->with('success', 'Análisis guardado, pero no se encontró la muestra para enviar notificación.');
            }

            // 1. Definir la CLAVE del proceso
            $claveProceso = 'MUESTRA_COMPLETA'; // Debe coincidir con la clave en 'cat_tipos_proceso'

            // 2. Obtener AMBAS listas de correos ('to' y 'cc')
            $recipients = ProcesoCorreo::getRecipientsByProcess($claveProceso);
            $listaTo = $recipients['to'] ?? [];
            $listaCc = $recipients['cc'] ?? [];

            // 3. (Opcional) Añadir el usuario que registró a la lista TO
            if ($muestraCompleta->usuarioOper && $muestraCompleta->usuarioOper->email) {
                // Evita añadirlo si ya está en la lista CC para no duplicar
                if(!in_array($muestraCompleta->usuarioOper->email, $listaCc)) {
                    $listaTo[] = $muestraCompleta->usuarioOper->email;
                }
            }

            // 4. Limpiar duplicados y nulos/vacíos de AMBAS listas
            $listaTo = array_values(array_unique(array_filter($listaTo))); // array_values para reindexar
            $listaCc = array_values(array_unique(array_filter($listaCc)));

            // 5. Asegurarse de que no enviemos CC si TO está vacío
            if (empty($listaTo)) {
                Log::info("No hay destinatarios principales (TO) para el proceso {$claveProceso} (Muestra ID: {$muestra->IdMuestra}). No se envió correo.");
                // Opcional: Si no hay TO, mover CC a TO
                 if (!empty($listaCc)) {
                    Log::info("Moviendo destinatarios CC a TO para Muestra ID: {$muestra->IdMuestra}");
                    $listaTo = $listaCc;
                    $listaCc = [];
                 }
            }

            // 6. Enviar el correo usando to() y cc()
            if (!empty($listaTo)) {
                // Obtener las reglas nuevamente para pasarlas al Mailable
                $reglas_para_mail = $this->obtenerCriterios($muestraCompleta->IdMaterial) ?? [];

                $mailInstance = Mail::to($listaTo);

                // Solo añadir CC si la lista no está vacía
                if (!empty($listaCc)) {
                    // Asegurarse que nadie esté en TO y CC al mismo tiempo (Laravel podría manejarlo, pero es buena práctica)
                    $listaCc_final = array_diff($listaCc, $listaTo);
                    if (!empty($listaCc_final)){
                        $mailInstance->cc($listaCc_final);
                    }
                }

                $mailInstance->send(new AnalisisCompletoMail($muestraCompleta, $reglas_para_mail));
                 Log::info("Correo enviado para Muestra ID {$muestra->IdMuestra}. TO: " . implode(', ', $listaTo) . (empty($listaCc_final) ? "" : " CC: " . implode(', ', $listaCc_final)) );
            }

        } catch (\Exception $e) {
            // Error específico en el envío de correo, pero el análisis se guardó
            Log::error("Error al enviar correo para Muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
            // Adjuntar el error al mensaje flash
            session()->flash('warning', 'Análisis guardado, pero hubo un error al enviar la notificación por correo: ' . $e->getMessage());
            // No usamos return aquí para que la redirección final ocurra
        }
        // --- FIN ACTUALIZACIÓN DE CORREO ---

        // Redirección final (se ejecuta incluso si falló el correo)
        // El mensaje de 'warning' se mostrará junto con el de 'success' si ambos existen
        return redirect()->route('dashboard')->with('success', 'Análisis de la muestra ' . $muestra->IdMuestra . ' guardado exitosamente.');
    } // Fin storeAnalisis

} // Fin MuestraController