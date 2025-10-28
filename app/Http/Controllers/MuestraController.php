<?php

namespace App\Http\Controllers;

// --- INCLUDES ---
use App\Models\Muestra;
use App\Models\Material; // Asegúrate que esté importado
use App\Models\Proveedor; // Asegúrate que esté importado (necesario para la etiqueta, si se usa)
use App\Models\Ubicacion; // Asegúrate que esté importado (necesario para la etiqueta, si se usa)
use App\Models\Elemento; // Necesario para obtener nombres de elementos
use App\Models\ResultadoAnalisis; // Necesario para guardar resultados
use App\Models\ProcesoCorreo; // Necesario para obtener destinatarios
use App\Mail\AnalisisCompletoMail; // Necesario para enviar el correo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Necesario para Auth::id()
use Illuminate\Support\Facades\DB; // Necesario para DB::transaction
use Illuminate\Support\Facades\Log; // Necesario para Log::error, Log::info, Log::warning
use Illuminate\Support\Facades\Mail; // Necesario para Mail::to()->send()
use Illuminate\Support\Facades\Validator; // Necesario para Validator::make()
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Importa la fachada del QR Code (NUEVO/Confirmado)


class MuestraController extends Controller
{
    /**
     * Esta función privada (la original) define los LÍMITES DE VALIDACIÓN (Cumple/No Cumple)
     * para la BD y el correo.
     */
    private function obtenerCriterios($id_material, $id_elemento = null)
    {
        // Array de criterios... (mantener toda tu lógica existente)
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
        // ... (Tu array de campos completo aquí)
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
        // Validación (ajusta según tus reglas y nombres de tabla/columna)
        $request->validate([
            'IdMaterial' => 'required|integer|exists:CatMateriales,IdMaterial',
            'Proveedor' => 'nullable|string|max:255',
            'Remision' => 'nullable|string|max:255',
             'PlacaTractor' => 'nullable|string|max:255',
             'PlacaTolva' => 'nullable|string|max:255',
             'Tonelaje' => 'nullable|numeric',
             'Solicitante' => 'nullable|string|max:255',
             'Area' => 'nullable|string|max:255',
             'Identificacion' => 'nullable|string|max:255',
             'Analisis' => 'nullable|string',
        ]);

        try {
            // Se crea la muestra y se guarda en la variable $muestra
            $muestra = Muestra::create([
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
                'IdEstatusAnalisis' => 1, // 1 = En Espera (valor por defecto)
                'IdUsuarioOper' => Auth::id(), 
                'FechaRegistro' => now(), 
            ]);

            // *** LÓGICA DE IMPRESIÓN ACTUALIZADA (Paso 4) ***
            // Redirigir al dashboard y enviar datos en la sesión
            // para mostrar el modal de impresión.
            return redirect()->route('dashboard')
                             ->with('success', '¡Muestra ' . $muestra->IdMuestra . ' registrada exitosamente!')
                             ->with('show_print_modal', true) // Bandera para activar el modal JS
                             ->with('print_label_url', route('muestras.etiqueta', $muestra->IdMuestra));
            // *** FIN LÓGICA DE IMPRESIÓN ACTUALIZADA ***

        } catch (\Exception $e) {
            Log::error("Error al registrar muestra: " . $e->getMessage());
            return redirect()->route('dashboard') 
                             ->withErrors(['general' => 'No se pudo registrar la muestra. Inténtalo de nuevo.'])
                             ->withInput(); 
        }
    }
    
    // ... (analisisIndex, fetchPendientes, rechazar, showAnalisisForm - sin cambios)
    
    public function analisisIndex()
    {
        // ... (Lógica de analisisIndex) ...
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                    ->where('IdEstatusAnalisis', 1)
                                    ->orderBy('FechaRegistro', 'asc')
                                    ->get();
        return view('muestras.analisis', compact('muestrasEnEspera'));
    }

    public function fetchPendientes()
    {
        // ... (Lógica de fetchPendientes) ...
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                    ->where('IdEstatusAnalisis', 1)
                                    ->orderBy('FechaRegistro', 'asc')
                                    ->get()
                                    ->toArray(); 

        // Mapeo manual
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
        // ... (Lógica de rechazar) ...
        if ($muestra) {
            try {
                $muestra->IdEstatusAnalisis = 3; // 3 = Rechazado
                $muestra->save();
                return redirect()->route('dashboard')->with('success', 'Muestra ' . $muestra->IdMuestra . ' ha sido rechazada.');
            } catch (\Exception $e) {
                 Log::error("Error al rechazar muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
                 return redirect()->route('dashboard')->with('error', 'No se pudo rechazar la muestra.');
            }
        }
        return redirect()->route('dashboard')->with('error', 'No se pudo encontrar la muestra a rechazar.');
    }

    public function showAnalisisForm(Muestra $muestra)
    {
        // ... (Lógica de showAnalisisForm) ...
        try {
            $muestra->load('material'); 
            $ids_elementos_a_mostrar = $this->obtenerCamposPorMaterial($muestra->IdMaterial);
            $reglas_validacion_material = $this->obtenerCriterios($muestra->IdMaterial); 
            $elementos_a_analizar = [];
            
            // ... (Lógica para obtener elementos y límites) ...

            $openWeatherApiKey = config('services.openweather.key');

            return view('muestras.analizar-form', compact('muestra', 'elementos_a_analizar', 'openWeatherApiKey'));

        } catch (\Exception $e) {
            Log::error("Error al mostrar formulario de análisis para muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'No se pudo cargar el formulario de análisis.');
        }
    }


    /**
     * Guarda los resultados del análisis en la BD y envía correo.
     */
    public function storeAnalisis(Request $request, Muestra $muestra)
    {
        // ... (Toda la lógica de Validación, DB::transaction y Lógica de Correo existente) ...
        
        // Redirección final
        return redirect()->route('dashboard')->with('success', 'Análisis de la muestra ' . $muestra->IdMuestra . ' guardado exitosamente.');
    } // Fin storeAnalisis

    
    // *** MÉTODO NUEVO PARA LA ETIQUETA (Paso 2) ***
    /**
     * Muestra la vista de la etiqueta imprimible con el QR.
     */
public function mostrarEtiqueta(Muestra $muestra)
    {
        // NOTA: Es crucial que las relaciones 'material' existan en App\Models\Muestra.php

        // Cargar la relación con el material para obtener el nombre
        $muestra->load('material'); 
        
        // Generar el contenido del QR
        // Asegúrate de tener el 'use SimpleSoftwareIO\QrCode\Facades\QrCode;' al inicio del archivo.
        $qrCode = QrCode::size(120) // Tamaño del QR en píxeles
                        ->margin(1) // Margen
                        ->generate((string)$muestra->IdMuestra); // Contenido del QR

        // Retornar la vista de la etiqueta
        return view('muestras.etiqueta', compact('muestra', 'qrCode'));

        /*
        // Si tienes problemas de permisos, puedes usar un bloque try-catch, 
        // pero DEBES regresar una respuesta simple, no una redirección.
        } catch (\Exception $e) {
            Log::error("Error al generar etiqueta para Muestra ID {$muestra->IdMuestra}: " . $e->getMessage());
            // Esto mostraría el mensaje en la ventana, en lugar de un error de Laravel.
            return response('Error: No se pudo generar la etiqueta. Ver logs para detalles.', 500);
        }
        */
    }

} // Fin MuestraController