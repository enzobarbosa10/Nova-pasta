<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

/**
 * VerifySupabaseJWT
 *
 * Validates the Supabase JWT (HS256) sent as a Bearer token.
 * On success, attaches the decoded user data to the request attributes
 * so downstream controllers can read them via:
 *   $request->attributes->get('supabase_user')
 *
 * SECURITY: Token signature is verified with SUPABASE_JWT_SECRET.
 * Expired, malformed or missing tokens are rejected with 401.
 */
class VerifySupabaseJWT
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token de autenticação não fornecido.'], 401);
        }

        $secret = env('SUPABASE_JWT_SECRET');

        if (! $secret) {
            return response()->json(['message' => 'Configuração de autenticação do servidor ausente.'], 500);
        }

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Supabase stores the application role in user_metadata
            $userMetadata = $decoded->user_metadata ?? null;
            $appRole      = $userMetadata->role ?? null;

            $request->attributes->set('supabase_user', [
                'id'    => $decoded->sub ?? null,
                'email' => $decoded->email ?? null,
                'role'  => $appRole,
            ]);

        } catch (ExpiredException) {
            return response()->json(['message' => 'Token expirado.'], 401);
        } catch (SignatureInvalidException) {
            return response()->json(['message' => 'Token inválido.'], 401);
        } catch (BeforeValidException) {
            return response()->json(['message' => 'Token ainda não é válido.'], 401);
        } catch (\UnexpectedValueException) {
            return response()->json(['message' => 'Token malformado.'], 401);
        } catch (\Exception) {
            return response()->json(['message' => 'Falha na autenticação.'], 401);
        }

        return $next($request);
    }
}
