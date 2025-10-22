<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material; // Este modelo ahora apunta a CatMateriales

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Datos para el formulario de registro
        // Esta línea ahora carga Material (CatMateriales) y su relación 'proveedores' (CatProveedores)
        $materiales = Material::with('proveedores') 
                              ->where('IdEstatus', 1)
                              ->orderBy('Material')
                              ->get();

        // 2. Datos para la tabla de muestras recientes
        $muestrasRecientes = Muestra::with(['material', 'estatusAnalisis', 'usuarioOper'])
                           ->orderBy('IdMuestra', 'desc')
                           ->take(10)
                           ->get();

        // 3. Datos para la tabla de muestras en espera
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1) 
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get();

        // 4. Enviamos todas las variables a la vista 'dashboard'
        return view('dashboard', compact('materiales', 'muestrasRecientes', 'muestrasEnEspera'));
    }
}