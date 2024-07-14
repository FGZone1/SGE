<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Http\Request;
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
 *     name="Comercios",
 *     description="Operaciones relacionadas con comercios"
 * )
 */
class ComercioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comercios",
     *     tags={"Comercios"},
     *     summary="Obtener todos los comercios",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comercios",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Comercio"))
     *     )
     * )
     */
    public function index()
    {
        return Comercio::all();
    }

    /**
     * @OA\Get(
     *     path="/api/comercios/{cuit}",
     *     tags={"Comercios"},
     *     summary="Obtener un comercio por CUIT",
     *     @OA\Parameter(
     *         name="cuit",
     *         in="path",
     *         required=true,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del comercio",
     *         @OA\JsonContent(ref="#/components/schemas/ComercioDetalles")
     *     ),
     *     @OA\Response(response=404, description="Comercio no encontrado")
     * )
     */
    public function show($cuit)
    {
        // Buscar el comercio por CUIT
        $comercio = Comercio::where('cuit', $cuit)->first();

        if (!$comercio) {
            return response()->json(['message' => 'Comercio no encontrado'], 404);
        }

        return response()->json([
            'razon_social' => $comercio->razon_social,
            'direccion' => $comercio->direccion,
            'estado' => $comercio->estado,
            'recarga_link' => url('/api/recarga/' . $comercio->cuit), // HATEOAS
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/comercios",
     *     tags={"Comercios"},
     *     summary="Registrar un nuevo comercio",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cuit", type="integer", description="CUIT del comercio"),
     *             @OA\Property(property="razon_social", type="string", description="Razón social del comercio"),
     *             @OA\Property(property="direccion", type="string", description="Dirección del comercio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comercio creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Comercio")
     *     ),
     *     @OA\Response(response=400, description="Datos inválidos")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cuit' => 'required|integer|unique:comercios',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $comercio = Comercio::create($request->all());
        return response()->json($comercio, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/comercios/{cuit}",
     *     tags={"Comercios"},
     *     summary="Actualizar un comercio",
     *     @OA\Parameter(
     *         name="cuit",
     *         in="path",
     *         required=true,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="razon_social", type="string", description="Razón social del comercio"),
     *             @OA\Property(property="direccion", type="string", description="Dirección del comercio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comercio actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comercio actualizado correctamente")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos inválidos"),
     *     @OA\Response(response=404, description="Comercio no encontrado")
     * )
     */
    public function update(Request $request, $cuit)
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'razon_social' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 400);
        }

        // Buscar el comercio por CUIT
        $comercio = Comercio::where('cuit', $cuit)->first();

        if (!$comercio) {
            return response()->json(['message' => 'Comercio no encontrado'], 404);
        }

        // Actualizar los campos
        $comercio->razon_social = $request->razon_social;
        $comercio->direccion = $request->direccion;

        // Guardar cambios
        $comercio->save();

        return response()->json(['message' => 'Comercio actualizado correctamente'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/comercios/{cuit}",
     *     tags={"Comercios"},
     *     summary="Suspender un comercio",
     *     @OA\Parameter(
     *         name="cuit",
     *         in="path",
     *         required=true,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comercio suspendido correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comercio suspendido correctamente")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Comercio no encontrado")
     * )
     */
    public function destroy($cuit)
    {
        $comercio = Comercio::find($cuit);
        if (!$comercio) {
            return response()->json(['message' => 'Comercio no encontrado'], 404);
        }

        // Marcar como suspendido
        $comercio->estado = 'suspendido';
        $comercio->save();

        return response()->json(['message' => 'Comercio suspendido correctamente'], 200);
    }
}
