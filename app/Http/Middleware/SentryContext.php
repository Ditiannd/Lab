<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\configureScope;

class SentryContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id', (string) Str::uuid());

        if (app()->bound('sentry')) {
            configureScope(function (Scope $scope) use ($request, $requestId): void {
                if ($user = $request->user()) {
                    $scope->setUser([
                        'id' => $user->id,
                        'username' => $user->name,
                        'email' => $user->email,
                    ]);
                }

                $scope->setTag('environment', app()->environment());
                $scope->setTag('app_version', (string) config('app.version'));
                $scope->setTag('request_id', $requestId);
                $scope->setTag('url', $request->fullUrl());
                $scope->setTag('ip', (string) $request->ip());

                $scope->setContext('request_details', [
                    'method' => $request->method(),
                    'route' => optional($request->route())->getName(),
                    'user_agent' => $request->userAgent(),
                ]);
            });
        }

        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}