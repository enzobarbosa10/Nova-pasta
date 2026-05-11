<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    // ---------------------------------------------------------------------------
    // Role constants
    // ---------------------------------------------------------------------------

    public const ROLE_MASTER_ADMIN = 'MASTER_ADMIN';
    public const ROLE_ADMIN        = 'ADMIN';
    public const ROLE_OPERATOR     = 'OPERATOR';
    public const ROLE_GUIDE        = 'GUIDE';
    public const ROLE_TRAVELER     = 'TRAVELER';

    /** Ordered display map used for labels and CASE WHEN sorting. */
    public const ROLES = [
        self::ROLE_MASTER_ADMIN => 'Master Admin',
        self::ROLE_ADMIN        => 'Administrador',
        self::ROLE_OPERATOR     => 'Operador',
        self::ROLE_GUIDE        => 'Guia',
        self::ROLE_TRAVELER     => 'Viajante',
    ];

    /**
     * Canonical role order used in orderedByRole() scope.
     * Index 0 = highest priority.
     */
    private const ROLE_ORDER = [
        self::ROLE_MASTER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_OPERATOR,
        self::ROLE_GUIDE,
        self::ROLE_TRAVELER,
    ];

    // ---------------------------------------------------------------------------
    // Fillable / Hidden / Casts
    // ---------------------------------------------------------------------------

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    // ---------------------------------------------------------------------------
    // Accessors / Business helpers
    // ---------------------------------------------------------------------------

    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function isMasterAdmin(): bool
    {
        return $this->role === self::ROLE_MASTER_ADMIN;
    }

    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    // ---------------------------------------------------------------------------
    // [ALTO 5] orderedByRole() scope — portable MySQL + PostgreSQL
    //
    // Replaces FIELD(role, ...) which is MySQL-only, with a CASE WHEN expression
    // that produces the same ordering on any SQL driver (MySQL, PostgreSQL,
    // SQLite, etc.).
    // ---------------------------------------------------------------------------

    /**
     * Order users by role hierarchy (MASTER_ADMIN first, TRAVELER last),
     * then alphabetically by name.
     *
     * Works identically on MySQL, PostgreSQL and SQLite.
     *
     * Usage:
     *   User::orderedByRole()->get();
     *   User::select(...)->orderedByRole()->orderBy('name')->paginate();
     */
    public function scopeOrderedByRole(Builder $query): Builder
    {
        $cases = collect(self::ROLE_ORDER)
            ->map(fn (string $role, int $index): string =>
                "WHEN role = '{$role}' THEN {$index}"
            )
            ->implode(' ');

        $lastIndex = count(self::ROLE_ORDER);

        return $query->orderByRaw("CASE {$cases} ELSE {$lastIndex} END");
    }
}
