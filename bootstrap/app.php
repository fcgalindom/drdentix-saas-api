<?php

use App\Http\Middleware\CompanyMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'company' => CompanyMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->render(function (QueryException $e, $request) {
            if (($request->is('api/*') || $request->expectsJson()) && ($e->errorInfo[0] ?? null) === '23505') {
                preg_match('/Key \((\w+)\)/', $e->getMessage(), $m);
                $field = $m[1] ?? 'campo';
                $labels = ['document' => 'documento', 'email' => 'correo electrónico'];
                $label = $labels[$field] ?? $field;
                return response()->json([
                    'message' => "El {$label} ya está registrado.",
                    'errors'  => [$field => ["El {$label} ya está en uso."]],
                ], 422);
            }
        });
    })->create();
