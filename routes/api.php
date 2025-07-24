<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\LigasController;
use App\Http\Controllers\Api\V1\UsuariosController;
use App\Http\Controllers\Api\V1\LicenciasController;
use App\Http\Controllers\Api\V1\ClientesController;

Route::middleware(['JWT'])->group(function () {
    Route::apiResource('licencias', LicenciasController::class);
    Route::apiResource('ligas', LigasController::class);
    Route::apiResource('usuarios', UsuariosController::class);
    Route::apiResource('clientes', ClientesController::class);
});

Route::post('login', [UsuariosController::class, 'login']);

// apiResource
// Route::get('ligas', [LigasController::class, 'index']);       // Obtener todos los recursos
// Route::post('ligas', [LigasController::class, 'store']);      // Crear nuevo recurso
// Route::get('ligas/{liga}', [LigasController::class, 'show']); // Mostrar recurso específico
// Route::put('ligas/{liga}', [LigasController::class, 'update']); // Actualizar recurso completo
// Route::patch('ligas/{liga}', [LigasController::class, 'update']); // Actualizar parcialmente
// Route::delete('ligas/{liga}', [LigasController::class, 'destroy']); // Eliminar recurso
