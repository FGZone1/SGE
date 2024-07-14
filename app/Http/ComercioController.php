<?php

namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ComercioController extends Controller
{
    public function index()
    {
        return Comercio::all();
    }

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
