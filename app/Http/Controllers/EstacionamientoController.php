<?php

namespace App\Http\Controllers;

use App\Models\Estacionamiento;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="API de Estacionamiento",
 *     version="1.0.0",
 *     description="API para gestionar estacionamientos y vehículos",
 *     )
 */
/**
 * @OA\Tag(
 *     name="Estacionamientos",
 *     description="Operaciones relacionadas con estacionamientos"
 * )
 */
class EstacionamientoController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/estacionamientos",
     *     tags={"Estacionamientos"},
     *     summary="Registrar un nuevo estacionamiento",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="dni", type="integer", description="DNI del usuario"),
     *             @OA\Property(property="patente", type="string", description="Patente del vehículo"),
     *             @OA\Property(property="contraseña", type="string", description="Contraseña del usuario"),
     *             @OA\Property(property="tiempo", type="integer", description="Tiempo de estacionamiento (en minutos)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Estacionamiento registrado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estacionamiento registrado correctamente")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Saldo insuficiente"),
     *     @OA\Response(response=404, description="Datos no válidos o usuario/vehículo no encontrado"),
     *     @OA\Response(response=409, description="La patente ya existe en el registro de estacionamiento")
     * )
     */
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

        if ($validator->fails()) {
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

    /**
     * @OA\Put(
     *     path="/api/estacionamientos/{patente}",
     *     tags={"Estacionamientos"},
     *     summary="Actualizar el estado de un estacionamiento",
     *     @OA\Parameter(
     *         name="patente",
     *         in="path",
     *         required=true,
     *         description="Patente del vehículo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="dni", type="integer", description="DNI del usuario"),
     *             @OA\Property(property="contraseña", type="string", description="Contraseña del usuario"),
     *             @OA\Property(property="estado", type="string", description="Estado del vehículo (estacionado/libre)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de estacionamiento actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estado de estacionamiento actualizado correctamente")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos"),
     *     @OA\Response(response=404, description="Usuario/vehículo no encontrado")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/estadoauto/{patente}",
     *     tags={"Estacionamientos"},
     *     summary="Obtener el estado de un vehículo",
     *     @OA\Parameter(
     *         name="patente",
     *         in="path",
     *         required=true,
     *         description="Patente del vehículo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="dni", type="integer", description="DNI del usuario"),
     *             @OA\Property(property="contraseña", type="string", description="Contraseña del usuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado del vehículo",
     *         @OA\JsonContent(
     *             @OA\Property(property="dni", type="integer"),
     *             @OA\Property(property="patente", type="string"),
     *             @OA\Property(property="estado", type="string"),
     *             @OA\Property(property="tiempo", type="integer"),
     *             @OA\Property(property="saldo", type="integer"),
     *             @OA\Property(property="_links", type="object", @OA\AdditionalProperties(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos"),
     *     @OA\Response(response=404, description="Usuario/vehículo no encontrado")
     * )
     */
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
