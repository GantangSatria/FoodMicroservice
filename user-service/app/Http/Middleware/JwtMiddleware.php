<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized - Token missing'], 401);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token"
            ])->post(env('AUTH_SERVICE_URL') . '/validate');

            if ($response->status() !== 200) {
                return response()->json(['message' => 'Unauthorized - Invalid token'], 401);
            }

            $request->attributes->add(['user' => $response->json()]);
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized - Auth service unreachable'], 401);
        }
    }
}
