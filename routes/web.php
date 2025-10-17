<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController; // <-- Corregido y añadido
use App\Http\Controllers\MuestraController;   // <-- Añadido para las muestras
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

// Ruta del Dashboard ahora apunta al DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- INICIO: Rutas para la Administración de Usuarios ---
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::get('/usuarios/{usuario}/permisos', [UsuarioController::class, 'editPermissions'])->name('usuarios.permissions.edit');
    Route::put('/usuarios/{usuario}/permisos', [UsuarioController::class, 'updatePermissions'])->name('usuarios.permissions.update');
    // --- FIN: Rutas de Administración de Usuarios ---

    // --- INICIO: Rutas para Muestras ---
    Route::get('/muestras/registro', [MuestraController::class, 'create'])->name('muestras.create');
    Route::post('/muestras/registro', [MuestraController::class, 'store'])->name('muestras.store');
    // --- FIN: Rutas para Muestras ---
});

require __DIR__.'/auth.php';