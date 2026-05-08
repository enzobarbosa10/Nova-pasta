<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Block access if the authenticated user's account is deactivated.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->active) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sua conta está desativada. Entre em contato com o administrador.',
            ], 403);
        }

        return $next($request);
    }
}
