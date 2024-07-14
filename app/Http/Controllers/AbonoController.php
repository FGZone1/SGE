<?php

namespace App\Http\Controllers;
use App\Models\Abono;
use Illuminate\Http\Request;
use App\Models\Comercio;
use App\Models\Recarga;
use Validator;

class AbonoController extends Controller
{
  // Método para obtener los abonos
  public function index(Request $request)
  {
    // el formato de fecha es yyyy-mm-dd en todo el programa
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
      //$importeTotal = $abonos->sum('importe');

     // return response()->json($abonos->sum('importe'));
      return response()->json([
        'importe_total' => $abonos->sum('importe'),
        '_links' => ['href' => url('/api/comercios/' . $request->cuit_comercio)]]);
  }

  // Método para registrar un abono
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

  // Método para calcular el importe esperado
  private function calcularImporteEsperado($cuit_comercio, $fecha_desde, $fecha_hasta)
  {
      // Realizar consulta a la tabla recargas y sumar todos los importes
      $importeTotal = Recarga::where('cuit_comercio', $cuit_comercio)
          ->whereBetween('creado', [$fecha_desde, $fecha_hasta])
          ->sum('importe');

      return $importeTotal;
  }
}