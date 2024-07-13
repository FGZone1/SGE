<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|integer',
            'contraseña' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Faltan datos obligatorios o los mismos no son correctos'], 404);
        }
    
        // Valida los datos de entrada
        $request->validate([
            'dni' => ['required'],
            'contraseña' => ['required'],
        ]);
    
        // Busca el usuario por DNI
        $user = User::where('dni', $request->dni)->first();
    
        // Verifica si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->contraseña, $user->contraseña)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        // Genera un token para el usuario autenticado
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
