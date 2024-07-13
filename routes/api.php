<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\EstacionamientoController;
use App\Http\Controllers\RecargaController;
use App\Http\Controllers\AbonoController;
use App\Http\Controllers\AuthController;

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



Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'chequearrol:auto,admin'])->group(function () {
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
 Route::get('/comercios', [ComercioController::class, 'index']);
 Route::get('/comercios/{cuit}', [ComercioController::class, 'show']);
 Route::post('/comercios', [ComercioController::class, 'store']);
 Route::put('/comercios/{cuit}', [ComercioController::class, 'update']);
 Route::delete('/comercios/{cuit}', [ComercioController::class, 'destroy']);
 
 // Estacionamientos
 Route::get('/estadoauto/{patente}', [EstacionamientoController::class, 'getEstado']);
 Route::post('/estacionamientos', [EstacionamientoController::class, 'store']);
 Route::put('/estacionamientos/{patente}', [EstacionamientoController::class, 'updateEstado']);


 
 // Recargas
 Route::post('/recargas', [RecargaController::class, 'store']);
 Route::get('/recargas', [RecargaController::class, 'index']);
 Route::get('/recargas/{patente}', [RecargaController::class, 'getSaldoPorPatente']);
});
Route::middleware(['auth:sanctum', 'chequearrol:negocio,admin'])->group(function () {
 // Abonos
 Route::get('/abonos', [AbonoController::class, 'index']);
 Route::post('/abonos', [AbonoController::class, 'store']);
});
