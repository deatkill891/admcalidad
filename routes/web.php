<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MuestraController;
use App\Http\Controllers\AnalisisHornoController;
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

// Ruta del Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grupo de rutas que requieren autenticación
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
    // (Estas rutas parecen ser del dashboard, las mantenemos)
    Route::get('/muestras/registro', [MuestraController::class, 'create'])->name('muestras.create');
    Route::post('/muestras/registro', [MuestraController::class, 'store'])->name('muestras.store');
    Route::get('/muestras/analisis', [MuestraController::class, 'analisisIndex'])->name('muestras.analisis');
    Route::get('/api/muestras-pendientes', [MuestraController::class, 'fetchPendientes'])->name('api.muestras.pendientes');
    Route::patch('/muestras/{muestra}/rechazar', [MuestraController::class, 'rechazar'])->name('muestras.rechazar');
    // --- FIN: Rutas para Muestras ---

    // --- INICIO: Rutas para la Analisis de muestras ---
    Route::get('/muestras/{muestra}/analizar', [MuestraController::class, 'showAnalisisForm'])->name('muestras.analizar.form');
    Route::post('/muestras/{muestra}/analizar', [MuestraController::class, 'storeAnalisis'])->name('muestras.analizar.store');
    // --- FIN: Rutas para Analisis de muestras ---

    // --- INICIO: Rutas para Análisis de Horno (HF, HA, MCC) ---
    Route::get('/analisis-horno/{tipo}', [AnalisisHornoController::class, 'create'])
        ->where('tipo', 'hf|ha|mcc')
        ->name('analisis-horno.create');

    Route::post('/analisis-horno', [AnalisisHornoController::class, 'store'])
        ->name('analisis-horno.store');
        
    // ** NUEVA RUTA (Añade esta línea) **
    // Usamos PATCH porque estamos "actualizando" el estatus, no borrando.
    Route::patch('/analisis-horno/{registro}/eliminar', [AnalisisHornoController::class, 'destroy'])
        ->name('analisis-horno.destroy');
    // --- FIN: Rutas para Análisis de Horno ---

});

// Archivo de rutas de autenticación
require __DIR__.'/auth.php';