<?php

namespace App\Http\Controllers;

use App\Models\Material; // <<-- ¡Añade esta línea!
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = Material::all(); // Ahora Laravel sabe dónde encontrar "Material"
        return view('materiales.index', compact('materiales'));
    }
    // ... otros métodos
}