<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('vehiculos')->get();
        return response()->json($usuarios);
    }

    public function show($dni)
    {
        $usuario = Usuario::with('vehiculos')->where('dni', $dni)->firstOrFail();
        $estadoEstacionamientoUrl = url("/api/estacionamientos?patente={$usuario->vehiculos->pluck('patente')->first()}");
        return response()->json([
            'usuario' => $usuario,
            'estado_estacionamiento' => $estadoEstacionamientoUrl
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|integer|unique:usuarios',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'domicilio' => 'string|max:255|nullable',
            'email' => 'required|email|unique:usuarios',
            'fecha_nacimiento' => 'required|date',
            'patente' => 'required|string|max:20|unique:vehiculos',
            'contraseña' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $usuario = Usuario::create([
                'dni' => $request->dni,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'domicilio' => $request->domicilio,
                'email' => $request->email,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'contraseña' => bcrypt($request->contraseña),
            ]);

            Vehiculo::create([
                'patente' => $request->patente,
                'dni_usuario' => $request->dni,
            ]);
        });

        return response()->json('Usuario y vehículo creados correctamente', 201);
    }

    public function update(Request $request, $dni)
    {
        $usuario = Usuario::with('vehiculos')->where('dni', $dni)->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'domicilio' => 'string|max:255|nullable',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->dni . ',dni',
            'fecha_nacimiento' => 'required|date',
            'patente' => 'required|string|max:20|unique:vehiculos,patente,' . $usuario->vehiculos->first()->patente . ',patente',
            'contraseña' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request, $usuario) {
            $usuario->update([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'domicilio' => $request->domicilio,
                'email' => $request->email,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'contraseña' => bcrypt($request->contraseña),
            ]);

            $usuario->vehiculos->first()->update([
                'patente' => $request->patente,
            ]);
        });

        return response()->json('Usuario y vehículo actualizados correctamente');
    }

    public function destroy($dni)
    {
        // Buscar el usuario por DNI
        $usuario = Usuario::find($dni);

        // Si no se encuentra, devolver un error 404
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Eliminar los vehículos asociados al usuario
        Vehiculo::where('dni_usuario', $dni)->delete();

        // Eliminar el usuario
        $usuario->delete();

        // Retornar una respuesta exitosa
        return response()->json(['message' => 'Usuario y vehículos eliminados con éxito'], 200);
    }
}
