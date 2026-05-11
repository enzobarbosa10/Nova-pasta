<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate a user and return a Sanctum token.
     * Rate-limited via RouteServiceProvider / routes definition.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        // Constant-time comparison — don't reveal whether the user exists
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (! $user->active) {
            return response()->json([
                'message' => 'Sua conta está desativada. Entre em contato com o administrador.',
            ], 403);
        }

        // Revoke all previous tokens unless "remember me" is checked
        if (! $request->boolean('remember')) {
            $user->tokens()->delete();
        }

        $cookieMinutes = $request->boolean('remember') ? 43200 : 480;
        $expiresAt     = $request->boolean('remember')
            ? now()->addDays(30)
            : now()->addHours(8);

        $token = $user->createToken('web_session', ['*'], $expiresAt)->plainTextToken;

        // Persist last login timestamp
        $user->updateQuietly(['last_login_at' => now()]);

        // SECURITY: Token emitted as HttpOnly + SameSite=Strict cookie only.
        // It is never exposed in the JSON body, preventing JavaScript from
        // reading or exfiltrating it (XSS mitigation). The browser sends the
        // cookie automatically on same-origin requests.
        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'role_label' => $user->getRoleLabel(),
            ],
        ])->cookie('api_token', $token, $cookieMinutes, '/', null, true, true, false, 'Strict');
    }

    /**
     * Revoke the current access token (logout) and expire the auth cookie.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sessão encerrada com sucesso.'])
            ->withoutCookie('api_token');
    }

    /**
     * Return the authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'role'          => $user->role,
            'role_label'    => $user->getRoleLabel(),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
        ]);
    }
}
