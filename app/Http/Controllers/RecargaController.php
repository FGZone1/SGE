<?php

namespace App\Http\Controllers;

use App\Models\Recarga;
use App\Models\Usuario;
use App\Models\Comercio;
use App\Models\Vehiculo;
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
 *     name="Recargas",
 *     description="Operaciones relacionadas con recargas de saldo"
 * )
 */
class RecargaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/recargas",
     *     tags={"Recargas"},
     *     summary="Agregar saldo a un usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cuit_comercio", type="integer", description="CUIT del comercio"),
     *             @OA\Property(property="dni", type="integer", description="DNI del usuario"),
     *             @OA\Property(property="importe", type="number", format="float", description="Importe a recargar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Saldo agregado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Saldo agregado correctamente")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos"),
     *     @OA\Response(response=403, description="Comercio no autorizado"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function store(Request $request)
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'cuit_comercio' => 'required|integer',
            'dni' => 'required|integer',
            'importe' => 'required|numeric|between:0,999999.99',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos no válidos'], 400);
        }

        // Verificar comercio
        $comercio = Comercio::where('cuit', $request->cuit_comercio)->where('estado', 'autorizado')->first();
        if (!$comercio) {
            return response()->json(['message' => 'Comercio no autorizado'], 403);
        }

        // Verificar usuario
        $usuario = Usuario::where('dni', $request->dni)->first();
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Agregar saldo
        $usuario->saldo += $request->importe;
        $usuario->save();

        // Registrar recarga
        Recarga::create([
            'dni_usuario' => $request->dni,
            'cuit_comercio' => $request->cuit_comercio,
            'importe' => $request->importe,
        ]);

        return response()->json(['message' => 'Saldo agregado correctamente'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/recargas",
     *     tags={"Recargas"},
     *     summary="Consultar recargas",
     *     @OA\Parameter(
     *         name="cuit_comercio",
     *         in="query",
     *         required=false,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dni_usuario",
     *         in="query",
     *         required=false,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="patente",
     *         in="query",
     *         required=false,
     *         description="Patente del vehículo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de recargas",
     *         @OA\JsonContent(
     *             @OA\Property(property="recargas", type="array", @OA\Items(type="number")),
     *             @OA\Property(property="_links", type="object", @OA\AdditionalProperties(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos"),
     *     @OA\Response(response=404, description="Usuario/vehículo no encontrado")
     * )
     */
    public function index(Request $request)
    {
        // Validar que se proporcione al menos un campo de filtro
        if (!$request->filled('cuit_comercio') && !$request->filled('dni') && !$request->filled('patente')) {
            return response()->json(['message' => 'Debe proporcionar al menos un dato de filtro'], 400);
        }

        // Validar entrada
        $validator = Validator::make($request->all(), [
            'cuit_comercio' => 'nullable|integer',
            'dni' => 'nullable|integer',
            'patente' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 400);
        }

        // Comprobar cuántos parámetros se han pasado
        $filterCount = $request->only('cuit_comercio', 'dni', 'patente');
        $filterCount = count(array_filter($filterCount));

        if ($filterCount > 1) {
            return response()->json(['message' => 'Solo se puede filtrar por un dato a la vez'], 400);
        } elseif ($filterCount == 0) {
            return response()->json(['message' => 'Al menos un dato a la vez'], 400);
        }

        // Buscar recargas
        $query = Recarga::query();

        if ($request->filled('cuit_comercio')) {
            $query->where('cuit_comercio', $request->cuit_comercio);
        } elseif ($request->filled('dni')) {
            $query->where('dni_usuario', $request->dni);
        } elseif ($request->filled('patente')) {
            // Obtener el DNI del usuario a partir de la patente
            $vehiculo = Vehiculo::where('patente', $request->patente)->first();

            if ($vehiculo) {
                $query->where('dni_usuario', $vehiculo->dni_usuario);
            } else {
                return response()->json(['message' => 'Patente no encontrada'], 404);
            }
        }

        // Seleccionar solo el importe
        $recargas = $query->pluck('importe');

        return response()->json([
            'recargas' => $recargas,
            '_links' => [
                'usuario' => [
                    'href' => url('/api/usuarios/' . $request->dni)
                ],
                'comercio' => [
                    'href' => url('/api/comercios/' . $request->cuit_comercio)
                ]
            ]
        ]);
    }
}
