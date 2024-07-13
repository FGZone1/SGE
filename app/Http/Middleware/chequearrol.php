<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class chequearrol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            \Log::info('Usuario no autenticado');
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $userRole = Auth::user()->rol;
        \Log::info("Rol del usuario: $userRole");
    
        if (!in_array($userRole, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        return $next($request);
    }
}
