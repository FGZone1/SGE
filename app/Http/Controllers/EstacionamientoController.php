<?php

namespace App\Http\Controllers;
use App\Models\Estacionamiento;
use Illuminate\Http\Request;

class EstacionamientoController extends Controller
{
    public function show($id)
    {
        return Estacionamiento::findOrFail($id);
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'dni' => 'required|integer|exists:usuarios,dni',
        'patente' => 'required|string|exists:vehiculos,patente',
        'contraseña' => 'required|string',
        'estado' => 'required|in:estacionado,libre',
        'tiempo' => 'required|integer|min:0',
    ]);

    // Verifica que el tiempo sea un múltiplo de 15
    if ($validatedData['tiempo'] % 15 !== 0) {
        return response()->json(['error' => 'El tiempo debe ser un múltiplo de 15 minutos.'], 400);
    }

    // Encuentra al usuario
    $usuario = Usuario::where('dni', $validatedData['dni'])->first();

    // Verifica la contraseña
    if (!Hash::check($validatedData['contraseña'], $usuario->contraseña)) {
        return response()->json(['error' => 'Contraseña incorrecta'], 401);
    }

    // Calcula el costo del estacionamiento
    $costoPorMinuto =100; // Ejemplo: 100 pesos por minuto
    $costoTotal = $validatedData['tiempo'] * $costoPorMinuto;

    // Verifica el saldo
    if ($usuario->saldo < $costoTotal) {
        $minutosAlcanzados = floor($usuario->saldo / $costoPorMinuto);
        return response()->json(['error' => 'Saldo insuficiente. Puedes estacionar por ' . $minutosAlcanzados . ' minutos.'], 400);
    }

    // Verifica si hay un estacionamiento activo para este vehículo
    $estacionamientoActivo = Estacionamiento::where('patente_vehiculo', $validatedData['patente'])
        ->where('estado', 'estacionado')
        ->first();

    if ($estacionamientoActivo) {
        return response()->json(['error' => 'El vehículo ya está estacionado.'], 400);
    }

    // Crea el registro de estacionamiento
    $estacionamiento = new Estacionamiento();
    $estacionamiento->patente_vehiculo = $validatedData['patente'];
    $estacionamiento->dni_usuario = $usuario->dni;
    $estacionamiento->estado = $validatedData['estado'];
    $estacionamiento->tiempo = $validatedData['tiempo'];
    $estacionamiento->save();

    // Actualiza el saldo del usuario
    $usuario->saldo -= $costoTotal;
    $usuario->save();

    return response()->json(['message' => 'Estacionamiento registrado exitosamente.'], 201);
}
    public function update(Request $request, $id)
    {
        $estacionamiento = Estacionamiento::findOrFail($id);
        $estacionamiento->update($request->all());
        return response()->json($estacionamiento, 200);
    }
}
