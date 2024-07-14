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
    
        // Busca el usuario por DNI
        $user = User::where('dni', $request->dni)->first();
    
        // Verifica si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($request->contraseña, $user->contraseña)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
    
        // Genera un token para el usuario autenticado
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Si llegamos aquí, el usuario está autenticado
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
    public function loggedUsers(Request $request)
{
    // Obtener todos los tokens activos
    $tokens = \DB::table('personal_access_tokens')->get();

    // Obtener los usuarios asociados a esos tokens
    $users = [];
    foreach ($tokens as $token) {
        $user = User::find($token->tokenable_id);
        if ($user) {
            $users[] = $user;
        }
    }

    return response()->json($users);
}
}
