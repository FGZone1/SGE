<?php

namespace App\Http\Controllers;
use App\Models\Comercio;
use Illuminate\Http\Request;

class ComercioController extends Controller
{
    public function show($cuit)
    {
        return Comercio::findOrFail($cuit);
    }

    public function store(Request $request)
    {
        $comercio = Comercio::create($request->all());
        return response()->json($comercio, 201);
    }

    public function update(Request $request, $cuit)
    {
        $comercio = Comercio::findOrFail($cuit);
        $comercio->update($request->all());
        return response()->json($comercio, 200);
    }

    public function destroy($cuit)
    {
        $comercio = Comercio::findOrFail($cuit);
        $comercio->delete();
        return response()->json(null, 204);
    }
}
