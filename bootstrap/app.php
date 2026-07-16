<?php

use App\Http\Middleware\EnsureApiRole;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SentryContext;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'api.role' => EnsureApiRole::class,
        ]);

        // Sanctum: dibutuhkan agar guard 'sanctum' bisa memvalidasi
        // cookie session (SPA) maupun personal access token (Bearer).
        $middleware->statefulApi();

        // GlitchTip: lekatkan user details + tags/context ke setiap
        // request, baik web maupun API.
        $middleware->web(append: [
            SentryContext::class,
        ]);

        $middleware->api(append: [
            SentryContext::class,
        ]);
    })
    
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Token tidak valid atau belum login.',
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Terjadi kesalahan pada request.',
                ], $e->getStatusCode());
            }
        });
    })->create();
