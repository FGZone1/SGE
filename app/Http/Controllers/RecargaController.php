<?php

namespace App\Http\Controllers;
use App\Models\Recarga;
use Illuminate\Http\Request;

class RecargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Recarga::query();

        if ($request->has('patente')) {
            $query->where('patente', $request->get('patente'));
        }

        if ($request->has('dni')) {
            $query->where('dni_usuario', $request->get('dni'));
        }

        if ($request->has('cuit')) {
            $query->where('cuit_comercio', $request->get('cuit'));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $recarga = Recarga::create($request->all());
        return response()->json($recarga, 201);
    }
}
