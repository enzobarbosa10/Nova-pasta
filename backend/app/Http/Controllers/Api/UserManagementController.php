<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * List all users (MASTER_ADMIN only).
     *
     * [ALTO 5] Uses User::orderedByRole() scope (CASE WHEN) instead of
     * FIELD() which is MySQL-only and breaks on PostgreSQL / Supabase.
     */
    public function index(): JsonResponse
    {
        $users = User::select(['id', 'name', 'email', 'role', 'active', 'last_login_at', 'created_at'])
            ->orderedByRole()   // portable CASE WHEN — works on MySQL + PostgreSQL + SQLite
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => array_merge($u->toArray(), [
                'role_label' => $u->getRoleLabel(),
            ]));

        return response()->json($users);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Prevent creating another MASTER_ADMIN through the API
        if ($request->role === User::ROLE_MASTER_ADMIN) {
            return response()->json([
                'message' => 'Não é possível criar um novo MASTER_ADMIN por esta interface.',
            ], 403);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'active'   => true,
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'user'    => array_merge(
                $user->only(['id', 'name', 'email', 'role', 'active', 'created_at']),
                ['role_label' => $user->getRoleLabel()]
            ),
        ], 201);
    }

    /**
     * Update user fields (name, email, role, active).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name'   => 'sometimes|required|string|max:255',
            'email'  => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'   => ['sometimes', 'required', Rule::in(array_keys(User::ROLES))],
            'active' => 'sometimes|boolean',
        ]);

        // Prevent downgrading MASTER_ADMIN role or assigning another MASTER_ADMIN
        if (isset($request->role)) {
            if ($user->isMasterAdmin() && $request->role !== User::ROLE_MASTER_ADMIN) {
                return response()->json([
                    'message' => 'O papel do MASTER_ADMIN não pode ser alterado.',
                ], 403);
            }
            if ($request->role === User::ROLE_MASTER_ADMIN && ! $user->isMasterAdmin()) {
                return response()->json([
                    'message' => 'Não é possível promover um usuário para MASTER_ADMIN por esta interface.',
                ], 403);
            }
        }

        $user->update($request->only(['name', 'email', 'role', 'active']));

        // Revoke tokens when account is deactivated
        if (isset($request->active) && ! $request->active) {
            $user->tokens()->delete();
        }

        $user->refresh();

        return response()->json([
            'message' => 'Usuário atualizado com sucesso.',
            'user'    => array_merge(
                $user->only(['id', 'name', 'email', 'role', 'active', 'last_login_at']),
                ['role_label' => $user->getRoleLabel()]
            ),
        ]);
    }

    /**
     * Reset a user's password (forces re-login).
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'password'              => 'required|string|min:8|max:255|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();

        return response()->json(['message' => 'Senha redefinida. O usuário precisará fazer login novamente.']);
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleActive(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Você não pode desativar sua própria conta.'], 403);
        }

        if ($user->isMasterAdmin()) {
            return response()->json(['message' => 'A conta MASTER_ADMIN não pode ser desativada.'], 403);
        }

        $user->update(['active' => ! $user->active]);

        if (! $user->active) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => $user->active ? 'Conta ativada.' : 'Conta desativada.',
            'active'  => $user->active,
        ]);
    }

    /**
     * Permanently delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta.'], 403);
        }

        if ($user->isMasterAdmin()) {
            return response()->json(['message' => 'A conta MASTER_ADMIN não pode ser excluída.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }
}
