<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use Illuminate\Http\Request;
use App\Models\Comercio;
use App\Models\Recarga;
use Validator;
/**
 * @OA\Info(
 *     title="API de Estacionamiento",
 *     version="1.0.0",
 *     description="API para gestionar estacionamientos y vehículos",
 *     )
 */

 /* @OA\Tag(
 *     name="Abonos",
 *     description="Operaciones relacionadas con abonos"
 * )
 */
class AbonoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/abonos",
     *     tags={"Abonos"},
     *     summary="Obtener abonos",
     *     @OA\Parameter(
     *         name="cuit_comercio",
     *         in="query",
     *         required=true,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fecha_desde",
     *         in="query",
     *         required=false,
     *         description="Fecha de inicio en formato yyyy-mm-dd",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="fecha_hasta",
     *         in="query",
     *         required=false,
     *         description="Fecha de fin en formato yyyy-mm-dd",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de abonos y el importe total",
     *         @OA\JsonContent(
     *             @OA\Property(property="importe_total", type="number", format="float"),
     *             @OA\Property(property="_links", type="object",
     *                 @OA\Property(property="href", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos")
     * )
     */
    public function index(Request $request)
    {
        // Validar que el campo 'cuit_comercio' esté presente en la consulta
        $validator = Validator::make($request->all(), [
            'cuit_comercio' => 'required|integer',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 400);
        }

        $consulta = Recarga::query();

        if ($request->filled('cuit_comercio')) {
            $consulta->where('cuit_comercio', $request->cuit_comercio);
        }

        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $consulta->whereBetween('creado', [$request->fecha_desde, $request->fecha_hasta]);
        }

        $abonos = $consulta->get();

        return response()->json([
            'importe_total' => $abonos->sum('importe'),
            '_links' => ['href' => url('/api/comercios/' . $request->cuit_comercio)]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/abonos",
     *     tags={"Abonos"},
     *     summary="Registrar un abono",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cuit_comercio", type="integer", description="CUIT del comercio"),
     *             @OA\Property(property="fecha_desde", type="string", format="date", description="Fecha de inicio"),
     *             @OA\Property(property="fecha_hasta", type="string", format="date", description="Fecha de fin"),
     *             @OA\Property(property="importe", type="number", format="float", description="Importe del abono")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abono registrado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Abono")
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos o comercio no autorizado")
     * )
     */
    public function store(Request $request)
    {
        // Validación de entrada
        $validador = Validator::make($request->all(), [
            'cuit_comercio' => 'required|integer',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
            'importe' => 'required|numeric',
        ]);

        if ($validador->fails()) {
            return response()->json(['message' => 'Datos no válidos'], 400);
        }

        // Verificar que el comercio exista y esté autorizado
        $comercio = Comercio::where('cuit', $request->cuit_comercio)->first();
        if (!$comercio || $comercio->estado !== 'autorizado') {
            return response()->json(['message' => 'Comercio no autorizado o inexistente'], 400);
        }

        // Calcular el importe esperado
        $importeEsperado = $this->calcularImporteEsperado($request->cuit_comercio, $request->fecha_desde, $request->fecha_hasta);
        if ($request->importe != $importeEsperado) {
            return response()->json(['message' => 'Importe incorrecto', "Es" => $importeEsperado], 400);
        }

        // Crear el abono
        $abono = Abono::create([
            'cuit_comercio' => $request->cuit_comercio,
            'fecha_desde' => $request->fecha_desde,
            'fecha_hasta' => $request->fecha_hasta,
            'importe' => $request->importe,
        ]);

        return response()->json($abono, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/abonos/",
     *     tags={"Abonos"},
     *     summary="Calcular importe esperado",
     *     @OA\Parameter(
     *         name="cuit_comercio",
     *         in="query",
     *         required=true,
     *         description="CUIT del comercio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="fecha_desde",
     *         in="query",
     *         required=true,
     *         description="Fecha de inicio en formato yyyy-mm-dd",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="fecha_hasta",
     *         in="query",
     *         required=true,
     *         description="Fecha de fin en formato yyyy-mm-dd",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Importe esperado calculado",
     *         @OA\JsonContent(
     *             @OA\Property(property="importe_esperado", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Datos no válidos")
     * )
     */
    private function calcularImporteEsperado($cuit_comercio, $fecha_desde, $fecha_hasta)
    {
        // Realizar consulta a la tabla recargas y sumar todos los importes
        $importeTotal = Recarga::where('cuit_comercio', $cuit_comercio)
            ->whereBetween('creado', [$fecha_desde, $fecha_hasta])
            ->sum('importe');

        return $importeTotal;
    }
}
