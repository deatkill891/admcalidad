<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material;
use App\Models\Elemento; // <-- Importante: Asegúrate de que este modelo exista
use App\Models\ResultadoAnalisis;
use App\Mail\AnalisisCompletoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

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
                return $reglas_material[$id_elemento] ?? null;
            }
            return $reglas_material;
        }
        return null;
    }

    /**
     * ¡NUEVA FUNCIÓN!
     * Define qué campos (elementos) se MUESTRAN en el formulario.
     * Lógica basada en 'elementosPorMaterial' de lab-form-analisis.php
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
            '20' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 16],
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
        $request->validate([
            'IdMaterial' => 'required|integer|exists:CatMateriales,IdMaterial',
            'Proveedor' => 'nullable|string|max:255',
            'Remision' => 'nullable|string|max:255',
        ]);
        Muestra::create([
            'IdMaterial' => $request->IdMaterial,
            'Proveedor' => $request->Proveedor,
            'Remision' => $request->Remision,
            'FechaRecibo' => now(),
            'PlacaTractor' => $request->PlacaTractor,
            'PlacaTolva' => $request->PlacaTolva,
            'Tonelaje' => $request->Tonelaje,
            'Solicitante' => $request->Solicitante,
            'Area' => $request->Area,
            'Identificacion' => $request->Identificacion,
            'Analisis' => $request->Analisis,
            'IdEstatusAnalisis' => 1, 'IdUsuarioOper' => Auth::id(),
            'FechaRegistro' => now(), 
        ]);
        return redirect()->route('dashboard')->with('success', '¡Muestra registrada exitosamente!');
    }

    public function analisisIndex()
    {
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1)
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get();
        return view('muestras.analisis', compact('muestrasEnEspera'));
    }

    public function fetchPendientes()
    {
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1) 
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get()
                                   ->toArray();

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
        if ($muestra) {
            $muestra->IdEstatusAnalisis = 3; // 3 = Rechazado
            $muestra->save();
            return redirect()->route('dashboard')->with('success', 'Muestra ' . $muestra->IdMuestra . ' ha sido rechazada.');
        }
        return redirect()->route('dashboard')->with('error', 'No se pudo encontrar la muestra a rechazar.');
    }

    /**
     * ¡MÉTODO ACTUALIZADO!
     * Muestra el formulario de análisis con los campos correctos.
     */
    public function showAnalisisForm(Muestra $muestra)
    {
        $muestra->load('material');

        // 1. OBTENER CAMPOS A MOSTRAR (Lógica de JS que proporcionaste)
        $ids_elementos_a_mostrar = $this->obtenerCamposPorMaterial($muestra->IdMaterial);

        // 2. OBTENER REGLAS DE VALIDACIÓN (Lógica de $criterios)
        $reglas_validacion = $this->obtenerCriterios($muestra->IdMaterial);
        
        $elementos_a_analizar = [];

        if (!empty($ids_elementos_a_mostrar)) {
            
            // 3. Obtener los nombres de los elementos de la BD
            $elementos_db = Elemento::whereIn('IdElemento', $ids_elementos_a_mostrar)
                                    ->get()
                                    ->keyBy('IdElemento');

            // 4. Combinar la lista de campos con sus reglas de validación
            foreach ($ids_elementos_a_mostrar as $id) {
                
                // Asegurarse de que el elemento exista en la BD
                if (isset($elementos_db[$id])) {
                    
                    // Buscar sus reglas de validación (si existen)
                    // Si un campo se muestra pero NO tiene criterio, usamos 0-100 como default
                    $limites = $reglas_validacion[$id] ?? ['min' => 0, 'max' => 100]; 

                    $elementos_a_analizar[] = [
                        'IdElemento' => $id,
                        'Nombre' => $elementos_db[$id]->Nombre,
                        'ValMin' => $limites['min'],
                        'ValMax' => $limites['max']
                    ];
                }
            }
        }
        
        // LÓGICA COMENTADA: Ya no se cambia el estatus al abrir el formulario.
        // El estatus solo debe cambiar al guardar el análisis (storeAnalisis) o al rechazar (rechazar).
        /*
        if ($muestra->IdEstatusAnalisis == 1) { 
            $muestra->IdEstatusAnalisis = 4; // 4 = En Proceso
            $muestra->save();
        }
        */
        
        $openWeatherApiKey = config('services.openweather.key');
        
        // 5. Pasar la lista correcta a la vista
        return view('muestras.analizar-form', compact('muestra', 'elementos_a_analizar', 'openWeatherApiKey'));
    }

    /**
     * Guarda los resultados del análisis en la base de datos Y ENVÍA EL CORREO.
     * (Este método NO necesita cambios, ya usa $criterios para validar)
     */
    public function storeAnalisis(Request $request, Muestra $muestra)
    {
        $reglas_material = $this->obtenerCriterios($muestra->IdMaterial);
        $rules = [];
        $messages = [];
        $elementos_map = [];

        if ($reglas_material) {
            $ids_elementos = array_keys($reglas_material);
            $elementos = Elemento::whereIn('IdElemento', $ids_elementos)->get()->keyBy('IdElemento');
            $elementos_map = $elementos; // Guardamos el mapa para la lógica de Polvo de Zinc

            foreach ($reglas_material as $idElemento => $limites) {
                // Validamos SÓLO si el campo fue enviado (está en $request->resultados)
                if (isset($request->resultados[$idElemento])) {
                    $nombre = $elementos[$idElemento]->Nombre ?? "Elemento $idElemento";
                    $min = $limites['min'];
                    $max = $limites['max'];

                    $rules["resultados.{$idElemento}.valor"] = "required|numeric|min:{$min}|max:{$max}";
                    $messages["resultados.{$idElemento}.valor.*"] = "El valor para {$nombre} debe estar entre {$min} y {$max}.";
                }
            }
        }

        $rules['clima'] = 'nullable|string|max:255';
        $rules['humedad'] = 'nullable|string|max:255';

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput(); 
        }

        foreach ($request->resultados as $idElemento => $resultado) {
            if (isset($resultado['valor']) && is_numeric($resultado['valor'])) {
                
                $valor_final = (float) $resultado['valor'];
                
                // Lógica de cálculo de Polvo de Zinc (Material ID 20)
                if ($muestra->IdMaterial == 20 && isset($elementos_map[$idElemento])) {
                    $nombreElemento = $elementos_map[$idElemento]->Nombre;
                    
                    switch ($nombreElemento) {
                        case 'Zn': $valor_final = round($valor_final * 0.8, 3); break;
                        case 'Pb': $valor_final = round($valor_final * 0.93, 3); break;
                        case 'Fe': $valor_final = round($valor_final * 0.7, 3); break;
                        case 'Cd': $valor_final = round($valor_final * 0.88, 3); break;
                        case 'Cu': $valor_final = round($valor_final * 0.8, 3); break;
                    }
                }

                ResultadoAnalisis::create([
                    'IdMuestra' => $muestra->IdMuestra,
                    'IdElemento' => $idElemento,
                    'Valor' => $valor_final,
                    'FechaRegistro' => now(),
                ]);
            }
        }

        $muestra->IdEstatusAnalisis = 2; // 2 = Analizado
        $muestra->IdUsuarioAnal = Auth::id(); 
        $muestra->FechaAnalisis = now();
        $muestra->Clima = $request->input('clima', 'N/A');
        $muestra->Humedad = $request->input('humedad', 'N/A');
        $muestra->save();

        // --- LÓGICA DE ENVÍO DE CORREO ---
        try {
            // Recargamos la muestra con TODAS las relaciones
            $muestraCompleta = Muestra::with(['material', 'usuarioOper', 'resultados.elemento'])
                                        ->find($muestra->IdMMuestra);

            // Replicamos la lista de correos de 'send-analysis-email.php'
            $listaEmails = [
                'dnavarro@deacero.com',
                'BSERRATO@deacero.com'
            ];
            
            // Añadimos el email del usuario que registró la muestra
            if ($muestraCompleta->usuarioOper && $muestraCompleta->usuarioOper->email) {
                $listaEmails[] = $muestraCompleta->usuarioOper->email;
            }

            if (count($listaEmails) > 0) {
                // Pasamos los límites (reglas) al Mailable
                Mail::to($listaEmails)->send(new AnalisisCompletoMail($muestraCompleta, $reglas_material ?? []));
            }

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('success', 'Análisis guardado, pero hubo un error al enviar el correo: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Análisis de la muestra ' . $muestra->IdMuestra . ' guardado y notificado por correo.');
    }
}