<?php

namespace App\Http\Controllers;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function show($patente)
    {
        return Vehiculo::where('patente', $patente)->firstOrFail();
    }

    public function store(Request $request)
    {
        $vehiculo = Vehiculo::create($request->all());
        return response()->json($vehiculo, 201);
    }

    public function update(Request $request, $patente)
    {
        $vehiculo = Vehiculo::where('patente', $patente)->firstOrFail();
        $vehiculo->update($request->all());
        return response()->json($vehiculo, 200);
    }

    public function destroy($patente)
    {
        $vehiculo = Vehiculo::where('patente', $patente)->firstOrFail();
        $vehiculo->delete();
        return response()->json(null, 204);
    }
}
