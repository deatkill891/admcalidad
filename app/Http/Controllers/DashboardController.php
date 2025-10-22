<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Muestra;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use App\Models\Permiso; // Asegúrate de importar Permiso

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Asegúrate que la relación 'permiso' exista en tu modelo User.php
        $permisos = $user->permiso; 

        // Determinar permisos específicos
        $puedeMuestreo = $permisos && $permisos->Muestreo == 1;
        $puedeChatarra = $permisos && $permisos->Chatarra == 1;
        
        // Determinar si el usuario puede registrar CUALQUIER tipo de muestra
        $puedeRegistrar = $puedeMuestreo || $puedeChatarra; 

        // 1. Filtrar Materiales según permisos y UserRol
        $materialesQuery = Material::where('IdEstatus', 1); // Empezar con materiales activos

        if ($puedeMuestreo && $puedeChatarra) {
            // Si tiene ambos permisos, mostrar UserRol 1 y 2
            $materialesQuery->whereIn('UserRol', [1, 2]);
        } elseif ($puedeMuestreo) {
            // Si solo tiene Muestreo, mostrar UserRol 1
            $materialesQuery->where('UserRol', 1);
        } elseif ($puedeChatarra) {
            // Si solo tiene Chatarra, mostrar UserRol 2
            $materialesQuery->where('UserRol', 2);
        } else {
            // Si no tiene ninguno, la consulta no devolverá nada (la tarjeta estará oculta de todos modos)
            $materialesQuery->whereRaw('1 = 0'); // Condición que siempre es falsa
        }

        // Ordenar y obtener los materiales filtrados
        $materiales = $materialesQuery->orderBy('Material')->get();

        // 2. Datos para la tabla de muestras recientes (sin cambios)
        $muestrasRecientes = Muestra::with(['material', 'estatusAnalisis', 'usuarioOper'])
                           ->orderBy('IdMuestra', 'desc')
                           ->take(10)
                           ->get();

        // 3. Datos para la tabla de muestras en espera (sin cambios)
        $muestrasEnEspera = Muestra::with(['material', 'usuarioOper'])
                                   ->where('IdEstatusAnalisis', 1)
                                   ->orderBy('FechaRegistro', 'asc')
                                   ->get();

        // 4. Enviar variables a la vista
        return view('dashboard', compact(
            'materiales',           // Ya filtrados
            'muestrasRecientes',
            'muestrasEnEspera',
            'puedeRegistrar'        // Para mostrar/ocultar la tarjeta
        ));
    }
}