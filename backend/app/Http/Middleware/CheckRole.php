<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verify that the authenticated user has one of the required roles.
     *
     * Usage in routes:  ->middleware('role:MASTER_ADMIN')
     *                   ->middleware('role:MASTER_ADMIN,ADMIN')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => 'Acesso não autorizado para esta função.',
            ], 403);
        }

        return $next($request);
    }
}
