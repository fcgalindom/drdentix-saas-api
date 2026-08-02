<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $user->company_id) {
            return response()->json(['message' => 'El usuario no tiene una compañía asignada.'], 403);
        }

        return $next($request);
    }
}
