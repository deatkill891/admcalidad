<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal, que funciona como un menú de navegación.
     */
    public function index()
    {
        // Esta función solo necesita retornar la vista del dashboard.
        // No necesita pasar ninguna variable o dato adicional.
        return view('dashboard');
    }
}