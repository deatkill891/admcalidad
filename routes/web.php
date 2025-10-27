<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MuestraController;
use App\Http\Controllers\AnalisisHornoController;
use App\Http\Controllers\ConfiguracionCorreoController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Rutas Administración de Usuarios ---
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::get('/usuarios/{usuario}/permisos', [UsuarioController::class, 'editPermissions'])->name('usuarios.permissions.edit');
    Route::put('/usuarios/{usuario}/permisos', [UsuarioController::class, 'updatePermissions'])->name('usuarios.permissions.update');
    
    // --- Rutas para Muestras ---
    Route::get('/muestras/registro', [MuestraController::class, 'create'])->name('muestras.create');
    Route::post('/muestras/registro', [MuestraController::class, 'store'])->name('muestras.store');
    Route::get('/muestras/analisis', [MuestraController::class, 'analisisIndex'])->name('muestras.analisis');
    Route::get('/api/muestras-pendientes', [MuestraController::class, 'fetchPendientes'])->name('api.muestras.pendientes');
    Route::patch('/muestras/{muestra}/rechazar', [MuestraController::class, 'rechazar'])->name('muestras.rechazar');
    Route::get('/muestras/{muestra}/analizar', [MuestraController::class, 'showAnalisisForm'])->name('muestras.analizar.form');
    Route::post('/muestras/{muestra}/analizar', [MuestraController::class, 'storeAnalisis'])->name('muestras.analizar.store');

    // --- Rutas para Análisis de Horno ---
    Route::get('/analisis-horno/{tipo}', [AnalisisHornoController::class, 'create'])
        ->where('tipo', 'hf|ha|mcc')
        ->name('analisis-horno.create');
    Route::post('/analisis-horno', [AnalisisHornoController::class, 'store'])
        ->name('analisis-horno.store');
    Route::patch('/analisis-horno/{registro}/eliminar', [AnalisisHornoController::class, 'destroy']) // <- Usamos el modelo OperAnalisisHorno aquí
        ->name('analisis-horno.destroy');


    // --- MÓDULO CONFIGURACIÓN DE CORREOS ---
    Route::prefix('configuracion-correos')->name('config.correos.')->group(function () {
        
        Route::get('/', [ConfiguracionCorreoController::class, 'index'])->name('index');
        Route::post('/store-correo', [ConfiguracionCorreoController::class, 'storeCorreo'])->name('store.correo');
        // Ruta para activar/desactivar (usando DELETE por convención RESTful, aunque actualice)
        Route::delete('/destroy-correo/{id}', [ConfiguracionCorreoController::class, 'destroyCorreo'])->name('destroy.correo');

        // ***** INICIO RUTAS NUEVAS *****
        // Ruta GET para obtener datos para el modal (AJAX)
        Route::get('/asignaciones/{idProceso}', [ConfiguracionCorreoController::class, 'getAsignacionesPorProceso'])
            ->name('get.asignaciones.process');

        // Ruta POST para guardar los datos del modal
        Route::post('/store-asignaciones-proceso', [ConfiguracionCorreoController::class, 'storeAsignacionesPorProceso'])
             ->name('store.asignaciones.process'); // <- ¡Este es el nombre que faltaba!
        // ***** FIN RUTAS NUEVAS *****

    });
    // --- FIN MÓDULO CONFIGURACIÓN DE CORREOS ---

});

require __DIR__.'/auth.php';