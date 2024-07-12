<?php

namespace App\Http\Controllers;
use App\Models\Estacionamiento;
use Illuminate\Http\Request;

class EstacionamientoController extends Controller
{
    public function show($id)
    {
        return Estacionamiento::findOrFail($id);
    }

    public function store(Request $request)
    {
        $estacionamiento = Estacionamiento::create($request->all());
        return response()->json($estacionamiento, 201);
    }

    public function update(Request $request, $id)
    {
        $estacionamiento = Estacionamiento::findOrFail($id);
        $estacionamiento->update($request->all());
        return response()->json($estacionamiento, 200);
    }
}
