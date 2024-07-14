<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

/**
 * @OA\Info(
 *     title="API de Estacionamiento",
 *     version="1.0.0",
 *     description="API para gestionar estacionamientos y vehículos",
 *     )
 */
/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Operaciones relacionadas con usuarios"
 * )
 */
class UsuarioController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/cambiarpatente/{dni}",
     *     tags={"Usuarios"},
     *     summary="Actualizar la patente de un vehículo",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="patente_actual", type="string", description="Patente actual del vehículo"),
     *             @OA\Property(property="patente_nueva", type="string", description="Nueva patente del vehículo")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Patente actualizada correctamente"),
     *     @OA\Response(response=404, description="Usuario o vehículo no encontrado"),
     *     @OA\Response(response=401, description="Contraseña incorrecta")
     * )
     */
    public function updatePatente(Request $request, $dni)
    {
        $usuario = Usuario::find($dni);
    
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    
        if (!Hash::check($request->input('contraseña'), $usuario->contraseña)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 401);
        }
    
        $validatedData = $request->validate([
            'patente_actual' => 'required|string',
            'patente_nueva' => 'required|string|unique:vehiculos,patente',
        ]);
    
        $vehiculo = Vehiculo::where('patente', $validatedData['patente_actual'])
                            ->where('dni_usuario', $dni)
                            ->first();
    
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado para este usuario'], 404);
        }
    
        $vehiculo->patente = $validatedData['patente_nueva'];
        $vehiculo->save();
    
        return response()->json(['message' => 'Patente actualizada correctamente'], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/cambiarclave/{dni}",
     *     tags={"Usuarios"},
     *     summary="Cambiar la contraseña de un usuario",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="contraseña_actual", type="string", description="Contraseña actual del usuario"),
     *             @OA\Property(property="contraseña_nueva", type="string", description="Nueva contraseña del usuario")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Contraseña actualizada correctamente"),
     *     @OA\Response(response=404, description="Usuario no encontrado"),
     *     @OA\Response(response=401, description="Contraseña actual incorrecta")
     * )
     */
    public function cambiarclave(Request $request, $dni)
    {
        $usuario = Usuario::find($dni);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if (!Hash::check($request->input('contraseña_actual'), $usuario->contraseña)) {
            return response()->json(['error' => 'Contraseña actual incorrecta'], 401);
        }

        $validatedData = $request->validate([
            'contraseña_nueva' => 'required|string|min:8',
        ]);

        $usuario->contraseña = Hash::make($validatedData['contraseña_nueva']);
        $usuario->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/usuarios",
     *     tags={"Usuarios"},
     *     summary="Obtener lista de usuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Usuario"))
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/usuarios/{dni}",
     *     tags={"Usuarios"},
     *     summary="Obtener información de un usuario específico",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Usuario")
     *     ),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show($dni)
    {
        $usuario = Usuario::find($dni);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $patentes = Vehiculo::where('dni_usuario', $dni)->pluck('patente');

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

    /**
     * @OA\Post(
     *     path="/api/usuarios",
     *     tags={"Usuarios"},
     *     summary="Crear un nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="dni", type="integer", description="DNI del usuario"),
     *             @OA\Property(property="nombre", type="string", description="Nombre del usuario"),
     *             @OA\Property(property="apellido", type="string", description="Apellido del usuario"),
     *             @OA\Property(property="domicilio", type="string", description="Domicilio del usuario"),
     *             @OA\Property(property="email", type="string", description="Email del usuario"),
     *             @OA\Property(property="fecha_nacimiento", type="string", format="date", description="Fecha de nacimiento"),
     *             @OA\Property(property="patente", type="string", description="Patente del vehículo"),
     *             @OA\Property(property="contraseña", type="string", description="Contraseña del usuario")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario y vehículo creados correctamente"),
     *     @OA\Response(response=404, description="Datos no válidos")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/usuarios/{dni}",
     *     tags={"Usuarios"},
     *     summary="Actualizar un usuario existente",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", description="Nombre del usuario"),
     *             @OA\Property(property="apellido", type="string", description="Apellido del usuario"),
     *             @OA\Property(property="domicilio", type="string", description="Domicilio del usuario"),
     *             @OA\Property(property="email", type="string", description="Email del usuario"),
     *             @OA\Property(property="fecha_nacimiento", type="string", format="date", description="Fecha de nacimiento")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado correctamente"),
     *     @OA\Response(response=404, description="Usuario no encontrado"),
     *     @OA\Response(response=401, description="Contraseña incorrecta")
     * )
     */
    public function update(Request $request, $dni)
    {
        $usuario = Usuario::find($dni);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if (!Hash::check($request->input('contraseña'), $usuario->contraseña)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 401);
        }

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'domicilio' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|email|max:100|unique:usuarios,email,' . $dni . ',dni',
            'fecha_nacimiento' => 'sometimes|required|date',
        ]);

        $usuario->update($validatedData);

        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/usuarios/{dni}",
     *     tags={"Usuarios"},
     *     summary="Eliminar un usuario",
     *     @OA\Parameter(
     *         name="dni",
     *         in="path",
     *         required=true,
     *         description="DNI del usuario",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuario y vehículos eliminados con éxito"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function destroy($dni)
    {
        $usuario = Usuario::find($dni);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        Vehiculo::where('dni_usuario', $dni)->delete();
        $usuario->delete();

        return response()->json(['message' => 'Usuario y vehículos eliminados con éxito'], 200);
    }
}
