<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class MuestraController extends Controller
{
    /**
     * Muestra el formulario de creación y la tabla de muestras recientes.
     */
    public function create()
    {
        // Preparamos los datos para los menús desplegables del formulario
        $materiales = Material::where('IdEstatus', 1)->orderBy('Material')->get();

        // Obtenemos las últimas 100 muestras para mostrar en la tabla
        $muestras = Muestra::with(['material', 'estatusAnalisis', 'usuarioOper'])
                           ->orderBy('IdMuestra', 'desc')
                           ->take(100)
                           ->get();

        // Enviamos todo a la vista
        return view('muestras.create', compact('materiales', 'muestras'));
    }

    /**
     * Guarda una nueva muestra en la base de datos.
     */
    public function store(Request $request)
    {
        // Validamos que los datos importantes vengan en el formulario
        $request->validate([
            'IdMaterial' => 'required|integer|exists:CatMateriales,IdMaterial',
            'FechaRecibo' => 'required|date',
            'Proveedor' => 'nullable|string|max:255',
            'Remision' => 'nullable|string|max:255',
        ]);

        // Creamos el registro en la tabla OperMuestras
        Muestra::create([
            'IdMaterial' => $request->IdMaterial,
            'Proveedor' => $request->Proveedor,
            'Remision' => $request->Remision,
            'FechaRecibo' => $request->FechaRecibo,
            'PlacaTractor' => $request->PlacaTractor,
            'PlacaTolva' => $request->PlacaTolva,
            'Tonelaje' => $request->Tonelaje,
            'Solicitante' => $request->Solicitante,
            'Area' => $request->Area,
            'Identificacion' => $request->Identificacion,
            'Analisis' => $request->Analisis,
            'Clima' => $request->Clima,
            'Humedad' => $request->Humedad,
            'IdEstatusAnalisis' => 1, // Por defecto 'Pendiente'
            'IdUsuarioOper' => Auth::id(), // ID del usuario que está logueado
            'FechaRegistro' => now(), // Fecha y hora actual
        ]);

        // Redirigimos de vuelta a la misma página con un mensaje de éxito
        return redirect()->route('muestras.create')->with('success', 'Muestra registrada exitosamente.');
    }
}