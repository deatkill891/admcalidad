<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal con el formulario y las tablas de muestras.
     */
    public function index()
    {
        // 1. Datos para el formulario de registro
        // ¡CAMBIO AQUÍ! Añade with('proveedores')
        $materiales = Material::with('proveedores') 
                              ->where('IdEstatus', 1)
                              ->orderBy('Material')
                              ->get();

        // 2. Datos para la tabla de muestras recientes (últimas 10)
        $muestrasRecientes = Muestra::with(['material', 'estatusAnalisis', 'usuarioOper'])
                           ->orderBy('IdMuestra', 'desc')
                           ->take(10)
                           ->get();

        // 3. Datos para la tabla de muestras en espera (pendientes)
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1) // 1 = Pendiente
                                   ->orderBy('FechaRegistro', 'asc') // Las más antiguas primero
                                   ->get();

        // 4. Enviamos todas las variables a la vista 'dashboard'
        return view('dashboard', compact('materiales', 'muestrasRecientes', 'muestrasEnEspera'));
    }
}