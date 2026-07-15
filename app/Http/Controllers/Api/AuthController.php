<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Login dan mendapatkan Bearer Token (Sanctum Personal Access Token).
     */
    public function login(LoginRequest $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Setiap login membuat token baru bernama sesuai device_name (default: "api").
        $token = $user->createToken($request->device_name ?? 'api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new UserResource($user->load('siswa')),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * GET /api/me
     * Data user yang sedang login (berdasarkan token).
     */
    public function me()
    {
        $user = Auth::user()->load('siswa');

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * POST /api/logout
     * Mencabut token yang sedang dipakai (device saat ini saja).
     */
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dicabut.',
        ]);
    }

    /**
     * POST /api/logout-all
     * Mencabut SEMUA token milik user (logout dari semua device).
     */
    public function logoutAll()
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout dari semua device berhasil.',
        ]);
    }
}
