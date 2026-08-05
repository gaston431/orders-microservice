<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtMiddleware
{
    /**
     * Intercepta la peticion y valida el token JWT de forma limpia
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token no proveido o mal formado'], 401);
        }

        // Extraemos el string puro del token de forma segura
        $parts = explode(' ', $authHeader);
        $jwt = isset($parts[1]) ? $parts[1] : null;

        if (!$jwt) {
            return response()->json(['error' => 'Token invalido'], 401);
        }

        try {
            // Validacion matematica local usando la clave secreta compartida
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

            // Inyeccion limpia utilizando la API nativa de Symfony/Laravel
            $request->attributes->set('user_id', $decoded->sub);
            $request->attributes->set('user_role', $decoded->role);
            $request->attributes->set('user_name', $decoded->name ?? null);
            $request->attributes->set('user_email', $decoded->email ?? null);

            // Validacion de autorizacion por roles
            if (!empty($roles) && !in_array($decoded->role, $roles)) {
                return response()->json(['error' => 'No tienes permisos para acceder a este recurso'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Token invalido o expirado'], 401);
        }

        return $next($request);
    }
}
