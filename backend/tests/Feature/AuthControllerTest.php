<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * AuthController feature tests.
 *
 * Covers: login (valid), wrong credentials, inactive account, logout, /me.
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // POST /api/v1/auth/login
    // =========================================================================

    public function test_login_with_valid_credentials_returns_user_and_cookie(): void
    {
        $user = User::factory()->admin()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'role', 'role_label']]);

        // HttpOnly cookie must be set — not the token in JSON body
        $this->assertNotNull($response->headers->getCookies());
        $cookies = collect($response->headers->getCookies())->keyBy('name');
        $this->assertTrue($cookies->has('api_token'), 'api_token cookie should be set');
        $this->assertTrue($cookies->get('api_token')->isHttpOnly(), 'cookie must be HttpOnly');
    }

    public function test_login_with_wrong_password_returns_422(): void
    {
        User::factory()->admin()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_nonexistent_email_returns_422(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'anypassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_inactive_account_returns_403(): void
    {
        User::factory()->inactive()->create([
            'email'    => 'inactive@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'inactive@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Sua conta está desativada. Entre em contato com o administrador.']);
    }

    public function test_login_throttle_blocks_after_10_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email'    => 'brute@example.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'brute@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    // =========================================================================
    // POST /api/v1/auth/logout
    // =========================================================================

    public function test_logout_revokes_token_and_clears_cookie(): void
    {
        $user = User::factory()->admin()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Sessão encerrada com sucesso.']);

        // Token should be gone from DB
        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    // =========================================================================
    // GET /api/v1/auth/me
    // =========================================================================

    public function test_me_returns_authenticated_user_profile(): void
    {
        $user = User::factory()->admin()->create(['name' => 'João Silva']);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonStructure(['id', 'name', 'email', 'role', 'role_label', 'last_login_at'])
            ->assertJsonFragment(['name' => 'João Silva']);
    }

    public function test_me_returns_401_without_auth(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}
