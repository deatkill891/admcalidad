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
        // Obtiene todos los usuarios y carga sus relaciones para evitar N+1 queries en la vista
        $usuarios = Usuario::with(['ubicacion', 'tipoUsuario'])->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Carga los datos de los catálogos para los selects.
     */
    public function create()
    {
        // Obtiene los catálogos de ubicaciones y tipos de usuario activos
        $ubicaciones = Ubicacion::where('IdEstatus', 1)->orderBy('Ubicacion')->get();
        $tiposUsuario = TipoUsuario::where('IdEstatus', 1)->orderBy('TipoUsuario')->get();

        // Pasa los datos de los catálogos a la vista 'create'
        return view('usuarios.create', compact('ubicaciones', 'tiposUsuario'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos y crea sus permisos por defecto.
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario, especificando la tabla para 'unique'
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:CatUsuarios,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:CatUsuarios,email'],
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
            'FechaRegistro' => now(), // Asumiendo que $timestamps=false en el modelo
        ]);

        // Se crea el registro de permisos para el nuevo usuario
        // y se inicializan todos los permisos en 0 por defecto.
        Permiso::create([
            'IdUsuario' => $usuario->IdUsuario, // Usa el ID del usuario recién creado
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
            // FechaActualizacion puede ser null inicialmente o now()
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente y permisos inicializados.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(Usuario $usuario)
    {
        // Carga los catálogos necesarios para los selects del formulario de edición
        $ubicaciones = Ubicacion::where('IdEstatus', 1)->orderBy('Ubicacion')->get();
        $tiposUsuario = TipoUsuario::where('IdEstatus', 1)->orderBy('TipoUsuario')->get();

        return view('usuarios.edit', compact('usuario', 'ubicaciones', 'tiposUsuario'));
    }

    /**
     * Actualiza la información de un usuario en la base de datos.
     */
    public function update(Request $request, Usuario $usuario)
    {
        // Valida los datos, ignorando el usuario actual en las reglas 'unique'
        $request->validate([
            // unique:<table>,<column>,<ignore_value>,<ignore_column>
            'username' => ['required', 'string', 'max:255', 'unique:CatUsuarios,username,'.$usuario->IdUsuario.',IdUsuario'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:CatUsuarios,email,'.$usuario->IdUsuario.',IdUsuario'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opcional
            'IdUbicacion' => ['required', 'integer', 'exists:CatUbicaciones,IdUbicacion'],
            'IdTipoUsuario' => ['required', 'integer', 'exists:CatTipoUsuarios,IdTipoUsuario'],
            'IdEstatus' => ['required', 'integer'], // Asumiendo que tienes un select para estatus
        ]);

        // Prepara los datos para actualizar el modelo Usuario
        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'IdUbicacion' => $request->IdUbicacion,
            'IdTipoUsuario' => $request->IdTipoUsuario,
            'IdEstatus' => $request->IdEstatus,
            // Agrega otros campos de CatUsuarios si los tienes en el formulario
        ];

        // Actualiza la contraseña solo si se proporcionó una nueva
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Actualiza el registro del usuario
        $usuario->update($updateData);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Muestra el formulario para editar los permisos de un usuario.
     */
    public function editPermissions(Usuario $usuario)
    {
        // Carga el usuario asegurándose de incluir la relación 'permiso' (singular)
        // load() añade la relación al modelo ya existente.
        $usuario->load('permiso'); // <-- CORRECCIÓN APLICADA AQUÍ

        // Pasa el usuario (con la relación 'permiso' cargada) a la vista
        return view('usuarios.permisos', compact('usuario'));
    }
    /**
     * Actualiza los permisos de un usuario.
     */
    public function updatePermissions(Request $request, Usuario $usuario)
    {
        // Prepara los datos de permisos basándose en los checkboxes del formulario
        // $request->has('nombre_checkbox') devuelve true si está marcado, false si no.
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
            'FechaActualizacion' => now(), // Actualiza la fecha de modificación
            // Podrías necesitar actualizar IdEstatus de CatPermisos aquí también si es relevante
            // 'IdEstatus' => $request->input('permisos_estatus', 1) // Ejemplo
        ];

        // Busca un registro en CatPermisos con el IdUsuario dado.
        // Si lo encuentra, lo actualiza con $permisosData.
        // Si no lo encuentra, crea un nuevo registro con ['IdUsuario' => $usuario->IdUsuario] + $permisosData.
        Permiso::updateOrCreate(
            ['IdUsuario' => $usuario->IdUsuario], // Criterio de búsqueda
            $permisosData                       // Datos para insertar o actualizar
        );

        return redirect()->route('usuarios.index')->with('success', 'Permisos actualizados correctamente.');
    }

    /**
     * Opcional: Método para eliminar un usuario.
     * Considera usar Soft Deletes si necesitas poder recuperarlos.
     */
    // public function destroy(Usuario $usuario)
    // {
    //     try {
    //         // Importante: Considera si necesitas eliminar los permisos primero
    //         // o si tienes configurada la eliminación en cascada en la BD.
    //         // $usuario->permiso()->delete(); // Si la relación se llama 'permiso'

    //         $usuario->delete(); // Elimina el usuario
    //         return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         // Captura errores de BD, como restricciones de llaves foráneas
    //         return redirect()->route('usuarios.index')->with('error', 'No se pudo eliminar el usuario. Puede estar asociado a otros registros.');
    //     } catch (\Exception $e) {
    //         // Captura otros errores generales
    //         return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error al eliminar el usuario.');
    //     }
    // }
}