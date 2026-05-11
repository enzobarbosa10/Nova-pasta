<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * UserManagementController feature tests.
 *
 * Covers: MASTER_ADMIN protection, list users, create, update, delete,
 * reset password, toggle active.
 */
class UserManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Role-based access control
    // =========================================================================

    public function test_index_requires_master_admin_role(): void
    {
        // ADMIN cannot access user management
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $this->getJson('/api/v1/users')->assertStatus(403);
    }

    public function test_operator_cannot_access_user_management(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'OPERATOR']), ['*']);

        $this->getJson('/api/v1/users')->assertStatus(403);
        $this->postJson('/api/v1/users', [])->assertStatus(403);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/users')->assertStatus(401);
    }

    // =========================================================================
    // GET /api/v1/users — index
    // =========================================================================

    public function test_master_admin_can_list_users(): void
    {
        User::factory()->count(5)->create();
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->getJson('/api/v1/users');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => ['id', 'name', 'email', 'role', 'active', 'role_label'],
            ]);
    }

    public function test_index_does_not_expose_passwords(): void
    {
        User::factory()->count(3)->create();
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->getJson('/api/v1/users');

        $response->assertOk();
        foreach ($response->json() as $user) {
            $this->assertArrayNotHasKey('password', $user);
        }
    }

    // =========================================================================
    // POST /api/v1/users — store
    // =========================================================================

    public function test_master_admin_can_create_operator(): void
    {
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->postJson('/api/v1/users', [
            'name'     => 'Novo Operador',
            'email'    => 'op@example.com',
            'password' => 'SuperSecret123!',
            'role'     => 'OPERATOR',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email', 'role']]);

        $this->assertDatabaseHas('users', ['email' => 'op@example.com', 'role' => 'OPERATOR']);
    }

    public function test_cannot_create_another_master_admin_via_api(): void
    {
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->postJson('/api/v1/users', [
            'name'     => 'Evil Master',
            'email'    => 'evil@example.com',
            'password' => 'SuperSecret123!',
            'role'     => 'MASTER_ADMIN',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'evil@example.com']);
    }

    public function test_store_validates_required_fields(): void
    {
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    public function test_store_prevents_duplicate_email(): void
    {
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->postJson('/api/v1/users', [
            'name'     => 'Dup',
            'email'    => 'dup@example.com',
            'password' => 'Secret123!',
            'role'     => 'ADMIN',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // =========================================================================
    // PUT /api/v1/users/{user} — update
    // =========================================================================

    public function test_master_admin_can_update_user(): void
    {
        $master = User::factory()->masterAdmin()->create();
        $user   = User::factory()->admin()->create(['name' => 'Old Name']);
        Sanctum::actingAs($master, ['*']);

        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_cannot_demote_master_admin(): void
    {
        $master      = User::factory()->masterAdmin()->create();
        $otherMaster = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->putJson("/api/v1/users/{$otherMaster->id}", [
            'role' => 'ADMIN',
        ]);

        $response->assertStatus(403);
    }

    // =========================================================================
    // DELETE /api/v1/users/{user} — destroy
    // =========================================================================

    public function test_master_admin_can_delete_user(): void
    {
        $master = User::factory()->masterAdmin()->create();
        $user   = User::factory()->admin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_cannot_delete_own_account(): void
    {
        $master = User::factory()->masterAdmin()->create();
        Sanctum::actingAs($master, ['*']);

        $response = $this->deleteJson("/api/v1/users/{$master->id}");

        $response->assertStatus(403);
    }

    // =========================================================================
    // PATCH /api/v1/users/{user}/toggle-active
    // =========================================================================

    public function test_can_toggle_user_active_status(): void
    {
        $master = User::factory()->masterAdmin()->create();
        $user   = User::factory()->admin()->create(['active' => true]);
        Sanctum::actingAs($master, ['*']);

        $response = $this->patchJson("/api/v1/users/{$user->id}/toggle-active");

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'active' => false]);
    }

    public function test_cannot_deactivate_own_account(): void
    {
        $master = User::factory()->masterAdmin()->create(['active' => true]);
        Sanctum::actingAs($master, ['*']);

        $response = $this->patchJson("/api/v1/users/{$master->id}/toggle-active");

        $response->assertStatus(403);
    }
}
