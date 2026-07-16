<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(['error' => 'Token no proveído o mal formado'], 401);
        }

        $jwt = $matches[1];

        try {
            // Validación matemática local sin consultar bases de datos externas
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));

            // Inyectar los datos del usuario en la petición para usarlo en los controladores si es necesario
            $request->attributes->add([
                'user_id' => $decoded->sub,
                'user_role' => $decoded->role,
                'user_name' => $decoded->name,  // <-- Capturado matemáticamente del token
                'user_email' => $decoded->email // <-- Capturado matemáticamente del token
            ]);

            // AUTORIZACIÓN: Validar si el rol del usuario está permitido para esta ruta
            if (!empty($roles) && !in_array($decoded->role, $roles)) {
                return response()->json(['error' => 'No tienes permisos para acceder a este recurso'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Token invalido o expirado'], 401);
        }

        return $next($request);
    }
}
