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
      $consulta = AbonoComercio::query();

      if ($request->filled('cuit_comercio')) {
          $consulta->where('cuit_comercio', $request->cuit_comercio);
      }

      if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
          $consulta->whereBetween('creado', [$request->fecha_desde, $request->fecha_hasta]);
      }

      $abonos = $consulta->get();

      return response()->json($abonos);
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
          return response()->json(['message' => 'Importe incorrecto'], 400);
      }

      // Crear el abono
      $abono = AbonoComercio::create([
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