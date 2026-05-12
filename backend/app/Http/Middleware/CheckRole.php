<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verify that the Supabase-authenticated user has one of the required roles.
     *
     * The role is read from the JWT user_metadata set by VerifySupabaseJWT.
     *
     * Usage in routes:  ->middleware('role:MASTER_ADMIN')
     *                   ->middleware('role:MASTER_ADMIN,ADMIN')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->attributes->get('supabase_user');

        if (! $user || ! in_array($user['role'], $roles)) {
            return response()->json([
                'message' => 'Acesso não autorizado para esta função.',
            ], 403);
        }

        return $next($request);
    }
}
