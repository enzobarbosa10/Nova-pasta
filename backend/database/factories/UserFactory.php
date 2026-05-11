<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id'                => Str::uuid(),
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => fake()->randomElement(['ADMIN', 'OPERATOR', 'GUIDE']),
            'active'            => true,
            'last_login_at'     => null,
        ];
    }

    public function masterAdmin(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_MASTER_ADMIN]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'ADMIN']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
