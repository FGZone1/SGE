<?php

namespace App\Http\Controllers;
use App\Models\Estacionamiento;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EstacionamientoController extends Controller
{
    
    public function store(Request $request)
{
    // Validación de entrada
    $validator = Validator::make( 
        $request->all(), [
        'dni' => 'required|integer',
        'patente' => 'required|string|max:20',
        'contraseña' => 'required|string',
        'tiempo' => 'required|integer|multiple_of:15',
    ]);
    if ( $validator->fails() == true ) {
        return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 404);
    }

   // Verificar si la patente ya existe en la tabla de estacionamientos
   $estacionamientoExistente = Estacionamiento::where('patente_vehiculo', $request->patente)->first();

   if ($estacionamientoExistente) {
       return response()->json(['message' => 'La patente ya existe en el registro de estacionamiento'], 409); 
   }   
  // Verificar si el vehículo existe con el DNI asociado
  $vehiculo = Vehiculo::where('patente', $request->patente)
  ->where('dni_usuario', $request->dni)
  ->first();

if (!$vehiculo) {
  return response()->json(['message' => 'La patente no esta entre sus autos'], 404);
}

// Verificar si el usuario existe
$usuario = Usuario::where('dni', $request->dni)->first();
if (!$usuario) {
  return response()->json(['message' => 'Usuario inexistente'], 404);
}

// Validar contraseña
if (!Hash::check($request->contraseña, $usuario->contraseña)) {
  return response()->json(['message' => 'Contraseña no válida'], 401);
}


   // Verificar saldo
    $costoPorMinuto = 40; 
    $costoTotal = $request->tiempo * $costoPorMinuto;

    if ($usuario->saldo < $costoTotal) {
        return response()->json(['message' => 'Saldo insuficiente'], 400);
    }

    
    // Registrar el estacionamiento
    Estacionamiento::create([
        'patente_vehiculo' => $request->patente,
        'dni_usuario' => $request->dni,
        'estado' => "estacionado",
        'tiempo' => $request->tiempo,
    ]);

    // Actualizar saldo
    $usuario->saldo -= $costoTotal;
    $usuario->save();
    
    return response()->json(['message' => 'Estacionamiento registrado correctamente'], 201);
}
    
public function updateEstado(Request $request, $patente)
{
    // Validación de entrada
    $validator = Validator::make(
        $request->all(), [
            'dni' => 'required|integer',
            'contraseña' => 'required|string',
            'estado' => 'required|in:estacionado,libre',
        ]
    );

    if ($validator->fails()) {
        return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 400);
    }

    // Verificar si el vehículo existe con el DNI asociado
    $vehiculo = Vehiculo::where('patente', $patente)
        ->where('dni_usuario', $request->dni)
        ->first();

    if (!$vehiculo) {
        return response()->json(['message' => 'La patente no está entre sus autos'], 404);
    }

    // Verificar si el usuario existe
    $usuario = Usuario::where('dni', $request->dni)->first();
    if (!$usuario) {
        return response()->json(['message' => 'Usuario inexistente'], 404);
    }

    // Validar contraseña
    if (!Hash::check($request->contraseña, $usuario->contraseña)) {
        return response()->json(['message' => 'Contraseña no válida'], 401);
    }

    // Verificar si el vehículo tiene un registro de estacionamiento
    $estacionamiento = Estacionamiento::where('patente_vehiculo', $patente)
        ->first();

    if (!$estacionamiento) {
        return response()->json(['message' => 'El vehículo no tiene un registro de estacionamiento'], 404);
    }

    // Verificar si el estado actual es diferente del estado solicitado
    if ($estacionamiento->estado == $request->estado) {
        return response()->json(['message' => 'El vehículo ya está en el estado solicitado'], 400);
    }

    // Actualizar el estado del estacionamiento
    Estacionamiento::where('patente_vehiculo', $patente)
        ->update(['estado' => $request->estado, 'actualizado' => now()]);

    return response()->json(['message' => 'Estado de estacionamiento actualizado correctamente'], 200);
}

public function getEstado($patente, Request $request)
{
    $validator = Validator::make($request->all(), [
        'dni' => 'required|integer',
        'contraseña' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 404);
    }

    $vehiculo = Vehiculo::where('patente', $patente)
        ->where('dni_usuario', $request->dni)
        ->first();

    if (!$vehiculo) {
        return response()->json(['message' => 'La patente no está entre sus autos'], 404);
    }

    $usuario = Usuario::where('dni', $request->dni)->first();

    if (!$usuario) {
        return response()->json(['message' => 'Usuario inexistente'], 404);
    }

    if (!Hash::check($request->contraseña, $usuario->contraseña)) {
        return response()->json(['message' => 'Contraseña no válida'], 401);
    }

    $estacionamiento = Estacionamiento::where('patente_vehiculo', $patente)
        ->where('dni_usuario', $request->dni)
        ->first();

    if (!$estacionamiento) {
        return response()->json(['message' => 'No hay registro de estacionamiento para este vehículo'], 404);
    }

    return response()->json([
        'dni' => $usuario->dni,
        'patente' => $estacionamiento->patente_vehiculo,
        'estado' => $estacionamiento->estado,
        'tiempo' => $estacionamiento->tiempo,
        'saldo' => $usuario->saldo,
        
        '_links' => [
                   
                'href' => url('/api/usuarios/' . $usuario->dni)
            
        ]
    ]);
}

    
}
