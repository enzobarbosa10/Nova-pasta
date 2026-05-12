<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AuthController
 *
 * Authentication is handled exclusively by Supabase Auth.
 * This controller only exposes /me which reads from the validated JWT.
 */
class AuthController extends Controller
{
    /**
     * Return the authenticated user data extracted from the Supabase JWT.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('supabase_user');

        return response()->json([
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);
    }
}
