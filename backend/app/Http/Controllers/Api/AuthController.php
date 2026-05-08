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

        $expiresAt = $request->boolean('remember')
            ? now()->addDays(30)
            : now()->addHours(8);

        $token = $user->createToken('web_session', ['*'], $expiresAt)->plainTextToken;

        // Persist last login timestamp
        $user->updateQuietly(['last_login_at' => now()]);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'role_label' => $user->getRoleLabel(),
            ],
        ]);
    }

    /**
     * Revoke the current access token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sessão encerrada com sucesso.']);
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
