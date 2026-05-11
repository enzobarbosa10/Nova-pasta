<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ReadTokenFromCookie
 *
 * Extracts the Sanctum personal-access token from the HttpOnly 'api_token'
 * cookie and injects it as a Bearer Authorization header, so that the
 * standard auth:sanctum guard can authenticate the request without the token
 * ever being readable by JavaScript.
 *
 * SECURITY: Token format is validated before use to prevent cookie-injection
 * attacks. Only applies when no Authorization header is already present.
 */
class ReadTokenFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken() && $request->hasCookie('api_token')) {
            $token = $request->cookie('api_token');

            // Sanctum personal-access token format: <id>|<40-char hash>
            if (is_string($token) && preg_match('/^\d+\|[a-zA-Z0-9]{40,}$/', $token)) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }
        }

        return $next($request);
    }
}
