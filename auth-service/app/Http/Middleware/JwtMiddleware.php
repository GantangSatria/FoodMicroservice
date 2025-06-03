<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return response()->json(['message' => 'Unauthorized - Token missing'], 401);
        }

        $token = $matches[1];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}"
            ])->post(env('AUTH_SERVICE_URL') . '/validate');

            if ($response->status() !== 200) {
                return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
            }

            // Optional: Attach user info to request
            $request->attributes->add(['user' => $response->json()]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized - Validation failed'], 401);
        }
    }
}
