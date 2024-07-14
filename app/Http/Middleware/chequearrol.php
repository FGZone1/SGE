<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class chequearrol
{
    protected $excludedRoutes = [
        'api/login', // Ruta de login
        //'api/usuarios', // Ruta de registro
        'api/logout',
       // 'api/user'
       'api/logged-users'
    ];
    /**
     * 
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica si la ruta actual está en la lista de exclusiones
        if (in_array($request->path(), $this->excludedRoutes)) {
            return $next($request); // Permite el acceso
        }
    
        // Log de la solicitud
        \Log::info($request->all());
    
        // Obtén el token del encabezado de autorización
        $token = $request->bearerToken();
        \Log::info('Token:', ['token' => $token]);
    
        try {
            // Verifica si el usuario está autenticado
            $user = $request->user();
    
            if (!$user) {
                \Log::error('Usuario no autenticado');
                return response()->json(['message' => 'Usuario no autenticado'], 401);
            }
    
            // Obtén el rol del usuario autenticado
            $userRole = $user->rol;
            \Log::info('Usuario autenticado:', ['user' => $user, 'role' => $userRole]);
    
            // Verifica si el rol del usuario está en la lista de roles permitidos
            if (!in_array($userRole, $roles)) {
                \Log::error('Unauthorized');
                return response()->json(['message' => 'Unauthorized'], 403);
            }
    
            \Log::info('Acceso permitido');
            return $next($request);
        } catch (\Exception $e) {
            \Log::error('Error de autenticación:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error de autenticación'], 500);
        }
    }
}