<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController; // <-- Se agregó este controlador
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- INICIO: Rutas agregadas para la Administración de Usuarios ---

    // Muestra la lista de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    // Muestra el formulario para crear un nuevo usuario
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');

    // Guarda el nuevo usuario en la base de datos
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    // Muestra el formulario para editar la información de un usuario
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');

    // Actualiza la información del usuario en la base de datos
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    
    // Muestra el formulario para editar los permisos de un usuario
    Route::get('/usuarios/{usuario}/permisos', [UsuarioController::class, 'editPermissions'])->name('usuarios.permissions.edit');

    // Actualiza los permisos del usuario en la base de datos
    Route::put('/usuarios/{usuario}/permisos', [UsuarioController::class, 'updatePermissions'])->name('usuarios.permissions.update');
    
    // --- FIN: Rutas agregadas para la Administración de Usuarios ---
});

require __DIR__.'/auth.php';