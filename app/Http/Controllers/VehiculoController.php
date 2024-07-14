<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API de Estacionamiento",
 *     version="1.0.0",
 *     description="API para gestionar estacionamientos y vehículos",
 *     )
 */
/**
 * @OA\Tag(
 *     name="Vehículos",
 *     description="Operaciones relacionadas con vehículos"
 * )
 */
class VehiculoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/vehiculos/{patente}",
     *     tags={"Vehículos"},
     *     summary="Obtener información de un vehículo específico",
     *     @OA\Parameter(
     *         name="patente",
     *         in="path",
     *         required=true,
     *         description="Patente del vehículo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Vehículo encontrado"),
     *     @OA\Response(response=404, description="Vehículo no encontrado")
     * )
     */
    public function show($patente)
    {
        return Vehiculo::where('patente', $patente)->firstOrFail();
    }

    /**
     * @OA\Post(
     *     path="/api/vehiculos",
     *     tags={"Vehículos"},
     *     summary="Crear un nuevo vehículo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="patente", type="string", description="Patente del vehículo"),
     *             @OA\Property(property="dni_usuario", type="integer", description="DNI del propietario del vehículo")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Vehículo creado correctamente"),
     *     @OA\Response(response=400, description="Datos no válidos")
     * )
     */
    public function store(Request $request)
    {
        $vehiculo = Vehiculo::create($request->all());
        return response()->json($vehiculo, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/vehiculos/{patente}",
     *     tags={"Vehículos"},
     *     summary="Actualizar un vehículo existente",
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
     *             @OA\Property(property="patente", type="string", description="Patente del vehículo"),
     *             @OA\Property(property="dni_usuario", type="integer", description="DNI del propietario del vehículo")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Vehículo actualizado correctamente"),
     *     @OA\Response(response=404, description="Vehículo no encontrado")
     * )
     */
    public function update(Request $request, $patente)
    {
        $vehiculo = Vehiculo::where('patente', $patente)->firstOrFail();
        $vehiculo->update($request->all());
        return response()->json($vehiculo, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/vehiculos/{patente}",
     *     tags={"Vehículos"},
     *     summary="Eliminar un vehículo",
     *     @OA\Parameter(
     *         name="patente",
     *         in="path",
     *         required=true,
     *         description="Patente del vehículo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=204, description="Vehículo eliminado con éxito"),
     *     @OA\Response(response=404, description="Vehículo no encontrado")
     * )
     */
    public function destroy($patente)
    {
        $vehiculo = Vehiculo::where('patente', $patente)->firstOrFail();
        $vehiculo->delete();
        return response()->json(null, 204);
    }
}
