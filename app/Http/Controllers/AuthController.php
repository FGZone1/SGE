<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'dni' => 'required|integer',
            'contraseña' => 'required|string',
        ]);

        $usuario = Usuario::where('dni', $request->dni)->first();

        if (!$usuario || !Hash::check($request->contraseña, $usuario->contraseña)) {
            return response()->json(['message' => 'Credenciales no válidas'], 401);
        }

        $token = $usuario->createToken('token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}

