<?php

namespace App\Http\Controllers;
use App\Models\Abono;
use Illuminate\Http\Request;

class AbonoController extends Controller
{
    public function index(Request $request)
    {
        $query = Abono::query();

        if ($request->has('cuit')) {
            $query->where('cuit_comercio', $request->get('cuit'));
        }

        if ($request->has('fecha_desde')) {
            $query->where('fecha_desde', '>=', $request->get('fecha_desde'));
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_hasta', '<=', $request->get('fecha_hasta'));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $abono = Abono::create($request->all());
        return response()->json($abono, 201);
    }
}
