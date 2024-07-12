<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\EstacionamientoController;
use App\Http\Controllers\RecargaController;
use App\Http\Controllers\AbonoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//}
//);
 // Usuarios
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::get('/usuarios/{dni}', [UsuarioController::class, 'show']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::put('/usuarios/{dni}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{dni}', [UsuarioController::class, 'destroy']);
Route::put('/cambiarclave/{dni}', [UsuarioController::class, 'cambiarclave']);
Route::put('/cambiarpatente/{dni}', [UsuarioController::class, 'updatePatente']);
 
 // Vehículos
 Route::get('/vehiculos/{patente}', [VehiculoController::class, 'show']);
 Route::post('/vehiculos', [VehiculoController::class, 'store']);
 Route::put('/vehiculos/{patente}', [VehiculoController::class, 'update']);
 Route::delete('/vehiculos/{patente}', [VehiculoController::class, 'destroy']);
 
 // Comercios
 Route::get('/comercios/{cuit}', [ComercioController::class, 'show']);
 Route::post('/comercios', [ComercioController::class, 'store']);
 Route::put('/comercios/{cuit}', [ComercioController::class, 'update']);
 Route::delete('/comercios/{cuit}', [ComercioController::class, 'destroy']);
 
 // Estacionamientos
 Route::get('/estacionamientos/{id}', [EstacionamientoController::class, 'show']);
 Route::post('/estacionamientos', [EstacionamientoController::class, 'store']);
 Route::put('/estacionamientos/{id}', [EstacionamientoController::class, 'update']);
 
 // Recargas
 Route::get('/recargas', [RecargaController::class, 'index']);
 Route::post('/recargas', [RecargaController::class, 'store']);
 
 // Abonos
 Route::get('/abonos', [AbonoController::class, 'index']);
 Route::post('/abonos', [AbonoController::class, 'store']);

