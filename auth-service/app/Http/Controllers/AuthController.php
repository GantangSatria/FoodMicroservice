<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;
use App\Models\UserToken;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    private $jwtSecret;
    private $jwtTTL;

    public function __construct()
    {
        $this->jwtSecret = env('JWT_SECRET');
        $this->jwtTTL = 3600; // 1 hour
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:auth_users,email',
            'password' => 'required|min:6',
            'name' => 'required',
            'gender' => 'required|in:male,female,other',
        ]);

        $uuid = Uuid::uuid4()->toString();

        $user = AuthUser::create([
            'uuid' => $uuid,
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
        ]);

        // Call user-service to create user profile
        Http::post(env('USER_SERVICE_URL') . '/users', [
            'uuid' => $uuid,
            'name' => $request->name,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $this->generateToken($user),
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = AuthUser::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $this->generateToken($user),
        ]);
    }

    public function validateToken(Request $request)
    {
        $token = $this->getToken($request);
        if (!$token) return response()->json(['message' => 'Token missing'], 401);

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));

            $jti = $decoded->jti ?? null;
            if (!$jti || UserToken::where('jwt_id', $jti)->where('is_revoked', true)->exists()) {
                return response()->json(['message' => 'Token revoked'], 401);
            }

            return response()->json([
                'uuid' => $decoded->sub,
                'email' => $decoded->email
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    }

    public function logout(Request $request)
    {
        $token = $this->getToken($request);
        if (!$token) return response()->json(['message' => 'Token missing'], 401);

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $jti = $decoded->jti ?? null;

            if ($jti) {
                UserToken::where('jwt_id', $jti)->update(['is_revoked' => true]);
            }

            return response()->json(['message' => 'Logged out']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
    }

    private function generateToken($user)
    {
        $jti = Uuid::uuid4()->toString();
        $iat = time();
        $exp = $iat + $this->jwtTTL;

        $payload = [
            'iss' => 'auth-service',
            'sub' => $user->uuid,
            'email' => $user->email,
            'iat' => $iat,
            'exp' => $exp,
            'jti' => $jti
        ];

        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

        UserToken::create([
            'user_uuid' => $user->uuid,
            'jwt_id' => $jti,
            'issued_at' => date('Y-m-d H:i:s', $iat),
            'expired_at' => date('Y-m-d H:i:s', $exp),
        ]);

        return $token;
    }

    private function getToken(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return null;
        }
        return $matches[1];
    }
}
