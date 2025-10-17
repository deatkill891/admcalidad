<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Permiso;
use App\Models\Ubicacion;
use App\Models\TipoUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsuarioController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     */
    public function index()
    {
        // Obtiene todos los usuarios y los pasa a la vista 'index'
        $usuarios = Usuario::all();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Carga los datos de los catálogos para los selects.
     */
    public function create()
    {
        // Obtiene los catálogos de ubicaciones y tipos de usuario
        $ubicaciones = Ubicacion::where('IdEstatus', 1)->orderBy('Ubicacion')->get();
        $tiposUsuario = TipoUsuario::where('IdEstatus', 1)->orderBy('TipoUsuario')->get();

        // Pasa los datos de los catálogos a la vista 'create'
        return view('usuarios.create', compact('ubicaciones', 'tiposUsuario'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:CatUsuarios'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:CatUsuarios'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'IdUbicacion' => ['required', 'integer', 'exists:CatUbicaciones,IdUbicacion'],
            'IdTipoUsuario' => ['required', 'integer', 'exists:CatTipoUsuarios,IdTipoUsuario'],
        ]);

        // Crea el usuario con los datos validados
        $usuario = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'IdUbicacion' => $request->IdUbicacion,
            'IdTipoUsuario' => $request->IdTipoUsuario,
            'IdEstatus' => 1, // Estatus activo por defecto
            'FechaRegistro' => now(),
        ]);

        // --- INICIO DE LA MODIFICACIÓN ---
        // Se crea el registro de permisos para el nuevo usuario
        // y se inicializan todos los permisos en 0 por defecto.
        Permiso::create([
            'IdUsuario' => $usuario->IdUsuario,
            'IdEstatus' => 1,
            'FechaRegistro' => now(),
            'Administrador' => 0,
            'Analisis' => 0,
            'Muestreo' => 0,
            'Insumos' => 0,
            'PolvoZn' => 0,
            'Chatarra' => 0,
            'CqElementos' => 0,
            'CqOxidos' => 0,
            'Metrologia' => 0,
            'Eads' => 0,
            'Evidencias' => 0,
        ]);
        // --- FIN DE LA MODIFICACIÓN ---

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza la información de un usuario en la base de datos.
     */
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.Usuario::class.',username,'.$usuario->IdUsuario.',IdUsuario'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Usuario::class.',email,'.$usuario->IdUsuario.',IdUsuario'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $usuario->username = $request->username;
        $usuario->email = $request->email;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Muestra el formulario para editar los permisos de un usuario.
     */
    public function editPermissions(Usuario $usuario)
    {
        // Carga el usuario con su relación de permisos
        $usuario->load('permisos');
        return view('usuarios.permisos', compact('usuario'));
    }

    /**
     * Actualiza los permisos de un usuario.
     */
    public function updatePermissions(Request $request, Usuario $usuario)
    {
        $permisosData = [
            'Administrador' => $request->has('Administrador') ? 1 : 0,
            'Analisis' => $request->has('Analisis') ? 1 : 0,
            'Muestreo' => $request->has('Muestreo') ? 1 : 0,
            'Insumos' => $request->has('Insumos') ? 1 : 0,
            'PolvoZn' => $request->has('PolvoZn') ? 1 : 0,
            'Chatarra' => $request->has('Chatarra') ? 1 : 0,
            'CqElementos' => $request->has('CqElementos') ? 1 : 0,
            'CqOxidos' => $request->has('CqOxidos') ? 1 : 0,
            'Metrologia' => $request->has('Metrologia') ? 1 : 0,
            'Eads' => $request->has('Eads') ? 1 : 0,
            'Evidencias' => $request->has('Evidencias') ? 1 : 0,
            'FechaActualizacion' => now(),
        ];
        
        // Actualiza o crea los permisos asociados al usuario
        $usuario->permisos()->updateOrCreate(['IdUsuario' => $usuario->IdUsuario], $permisosData);
    
        return redirect()->route('usuarios.index')->with('success', 'Permisos actualizados correctamente.');
    }
}