<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware role khusus untuk /api/*.
 *
 * Berbeda dengan App\Http\Middleware\RoleMiddleware (dipakai di web,
 * yang me-redirect ke halaman login), middleware ini selalu membalas
 * JSON sehingga aman dipakai oleh Bruno/Postman atau aplikasi mobile.
 *
 * Contoh pemakaian di routes/api.php:
 *   Route::middleware(['auth:sanctum', 'api.role:admin,petugas'])->group(...)
 */
class EnsureApiRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Sertakan header Authorization: Bearer {token}.',
            ], 401);
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Role Anda ('.$user->role.') tidak memiliki akses ke resource ini.',
                'required_role' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
