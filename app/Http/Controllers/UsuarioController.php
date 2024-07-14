<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class UsuarioController extends Controller
{
    public function updatePatente(Request $request, $dni)
    {
        $usuario = Usuario::find($dni);
    
        // Verifica si el usuario existe
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    
        // Valida la contraseña
        if (!Hash::check($request->input('contraseña'), $usuario->contraseña)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 401);
        }
    
        // Valida los datos de entrada
        $validatedData = $request->validate([
            'patente_actual' => 'required|string',
            'patente_nueva' => 'required|string|unique:vehiculos,patente',
        ]);
    
        // Busca el vehículo por la patente actual
        $vehiculo = Vehiculo::where('patente', $validatedData['patente_actual'])
                            ->where('dni_usuario', $dni)
                            ->first();
    
        // Verifica si el vehículo existe
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado para este usuario'], 404);
        }
    
        // Actualiza la patente
        $vehiculo->patente = $validatedData['patente_nueva'];
        $vehiculo->save();
    
        return response()->json(['message' => 'Patente actualizada correctamente'], 200);
    }

    public function cambiarclave(Request $request, $dni)
{
    $usuario = Usuario::find($dni);

    // Verifica si el usuario existe
    if (!$usuario) {
        return response()->json(['error' => 'Usuario no encontrado'], 404);
    }

    // Valida la contraseña actual
    if (!Hash::check($request->input('contraseña_actual'), $usuario->contraseña)) {
        return response()->json(['error' => 'Contraseña actual incorrecta'], 401);
    }

    // Valida la nueva contraseña
    $validatedData = $request->validate([
        'contraseña_nueva' => 'required|string|min:8',
    ]);

    // Actualiza la contraseña
    $usuario->contraseña = Hash::make($validatedData['contraseña_nueva']);
    $usuario->save();

    return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
}

    public function index()
{
    $usuarios = Usuario::all();

    $response = $usuarios->map(function ($usuario) {
        $patentes = Vehiculo::where('dni_usuario', $usuario->dni)->pluck('patente');

        $links = [];
        foreach ($patentes as $patente) {
            $links[] = [
                'rel' => 'estacionamiento',
                'href' => url('/api/estacionamientos/' . $patente),
            ];
        }

        return [
            'dni' => $usuario->dni,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'domicilio' => $usuario->domicilio,
            'email' => $usuario->email,
            'fecha_nacimiento' => $usuario->fecha_nacimiento,
            'patentes' => $patentes,
            'saldo' => $usuario->saldo,
            'links' => $links,
        ];
    });

    return response()->json($response, 200);
}
    public function show($dni)
    {
        $usuario = Usuario::find($dni);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $patentes = Vehiculo::where('dni_usuario', $dni)->pluck('patente');

        // Construir los links HATEOAS para todas las patentes
        $links = [];
        foreach ($patentes as $patente) {
            $links[] = [
                'rel' => 'estacionamiento',
                'href' => url('/api/estacionamientos/' . $patente),
            ];
        }

        $response = [
            'dni' => $usuario->dni,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'domicilio' => $usuario->domicilio,
            'email' => $usuario->email,
            'fecha_nacimiento' => $usuario->fecha_nacimiento,
            'patentes' => $patentes,
            'saldo' => $usuario->saldo,
            'links' => $links,
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|integer|unique:usuarios',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'domicilio' => 'string|max:255|nullable',
            'email' => 'required|email|unique:usuarios',
            'fecha_nacimiento' => 'required|date',
            'patente' => 'required|string|max:20|unique:vehiculos',
            'contraseña' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 404);
        }
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
                'contraseña' => Hash::make($request->contraseña),
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
        $usuario = Usuario::find($dni);

        // Verifica si el usuario existe
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Valida la contraseña
        if (!Hash::check($request->input('contraseña'), $usuario->contraseña)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 401);
        }

        // Valida los datos de entrada
        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'domicilio' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|email|max:100|unique:usuarios,email,' . $dni . ',dni',
            'fecha_nacimiento' => 'sometimes|required|date',
        ]);

        // Actualiza el usuario
        $usuario->update($validatedData);

        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
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
