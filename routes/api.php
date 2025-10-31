<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\LigasController;
use App\Http\Controllers\Api\V1\UsuariosController;
use App\Http\Controllers\Api\V1\LicenciasController;
use App\Http\Controllers\Api\V1\ClientesController;
use App\Http\Controllers\Api\V1\EntrenosController;
use App\Http\Controllers\Api\V1\MenusController;
use App\Http\Controllers\Api\V1\PagosController;
use App\Http\Controllers\Api\V1\RolesController;
use App\Http\Controllers\Api\V1\PermisosRolController;
use App\Services\RouteGeneratorService;
use Illuminate\Support\Facades\Request;

Route::middleware(['JWT'])->group(function () {
    Route::apiResource('licencias', LicenciasController::class);
    Route::apiResource('ligas', LigasController::class);
    Route::apiResource('usuarios', UsuariosController::class);
    Route::apiResource('clientes', ClientesController::class);
    Route::apiResource('entrenos', EntrenosController::class);
    Route::apiResource('menus', MenusController::class);
    Route::apiResource('pagos', PagosController::class);
    Route::apiResource('roles', RolesController::class);
    Route::apiResource('permiso-rol', PermisosRolController::class);

    Route::post('permiso-rol/menus/{idRol}', [PermisosRolController::class, 'permisosUsuario']);
});

Route::post('login', [UsuariosController::class, 'login']);
Route::post('refresh-token', [UsuariosController::class, 'refreshToken']);


// apiResource
// Route::get('ligas', [LigasController::class, 'index']);       // Obtener todos los recursos
// Route::post('ligas', [LigasController::class, 'store']);      // Crear nuevo recurso
// Route::get('ligas/{liga}', [LigasController::class, 'show']); // Mostrar recurso específico
// Route::put('ligas/{liga}', [LigasController::class, 'update']); // Actualizar recurso completo
// Route::patch('ligas/{liga}', [LigasController::class, 'update']); // Actualizar parcialmente
// Route::delete('ligas/{liga}', [LigasController::class, 'destroy']); // Eliminar recurso

Route::get('/create', function (Request $request) {
    $RouteGenerator = new RouteGeneratorService();
    return $RouteGenerator->crearRutaYControlador('/url-externa', 'https://api.bigdatacloud.net/data/reverse-geocode-client', 'get', 'controlador');
});

Route::get('/elimina', function (Request $request) {
    $RouteGenerator = new RouteGeneratorService();
    return $RouteGenerator->eliminarRutaYControlador('get', 'controlador');
});
