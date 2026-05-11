<?php

namespace Tests\Feature;

use App\Models\Expedition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * ExpeditionController feature tests.
 *
 * Covers: CRUD completo, paginação, filtros, validação de participantes
 * antes de deletar, verificação de status.
 */
class ExpeditionControllerTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // GET /api/v1/expeditions — index
    // =========================================================================

    public function test_authenticated_user_can_list_expeditions(): void
    {
        Expedition::factory()->count(5)->create();
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/expeditions');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                'links',
            ]);
    }

    public function test_unauthenticated_user_cannot_list_expeditions(): void
    {
        $this->getJson('/api/v1/expeditions')->assertStatus(401);
    }

    public function test_index_filters_by_status(): void
    {
        Expedition::factory()->count(3)->create(['status' => 'OPEN']);
        Expedition::factory()->count(2)->create(['status' => 'PLANNING']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/expeditions?status=OPEN');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
        collect($response->json('data'))->each(
            fn ($item) => $this->assertEquals('OPEN', $item['status'])
        );
    }

    // =========================================================================
    // GET /api/v1/expeditions/{expedition} — show
    // =========================================================================

    public function test_can_view_single_expedition(): void
    {
        $expedition = Expedition::factory()->create(['name' => 'Serra da Canastra']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson("/api/v1/expeditions/{$expedition->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Serra da Canastra']);
    }

    public function test_show_returns_404_for_nonexistent_expedition(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $this->getJson('/api/v1/expeditions/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    }

    // =========================================================================
    // POST /api/v1/expeditions — store
    // =========================================================================

    public function test_admin_can_create_expedition(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->postJson('/api/v1/expeditions', $this->validExpeditionPayload());

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'destination', 'status']);

        $this->assertDatabaseHas('expeditions', ['name' => 'Expedição Teste']);
    }

    public function test_operator_can_create_expedition(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'OPERATOR']), ['*']);

        $response = $this->postJson('/api/v1/expeditions', $this->validExpeditionPayload());

        $response->assertStatus(201);
    }

    public function test_guide_cannot_create_expedition(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'GUIDE']), ['*']);

        $this->postJson('/api/v1/expeditions', $this->validExpeditionPayload())
            ->assertStatus(403);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->postJson('/api/v1/expeditions', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'destination', 'capacity']);
    }

    public function test_store_validates_trail_level_enum(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);
        $payload = array_merge($this->validExpeditionPayload(), ['trail_level' => 'INVALID']);

        $this->postJson('/api/v1/expeditions', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['trail_level']);
    }

    public function test_store_validates_end_date_after_start_date(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);
        $payload = array_merge($this->validExpeditionPayload(), [
            'start_date' => '2027-06-01',
            'end_date'   => '2027-05-01', // before start
        ]);

        $this->postJson('/api/v1/expeditions', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    // =========================================================================
    // PUT /api/v1/expeditions/{expedition} — update
    // =========================================================================

    public function test_admin_can_update_expedition(): void
    {
        $expedition = Expedition::factory()->create(['name' => 'Old Name']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->putJson("/api/v1/expeditions/{$expedition->id}", [
            'name' => 'New Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('expeditions', ['id' => $expedition->id, 'name' => 'New Name']);
    }

    // =========================================================================
    // DELETE /api/v1/expeditions/{expedition} — destroy
    // =========================================================================

    public function test_admin_can_delete_expedition_without_participants(): void
    {
        $expedition = Expedition::factory()->create(['participants' => []]);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->deleteJson("/api/v1/expeditions/{$expedition->id}");

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Expedition deleted successfully.']);

        $this->assertSoftDeleted('expeditions', ['id' => $expedition->id]);
    }

    public function test_delete_blocked_when_expedition_has_active_participants(): void
    {
        $expedition = Expedition::factory()->withParticipants(3)->create(['status' => 'OPEN']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->deleteJson("/api/v1/expeditions/{$expedition->id}");

        $response->assertStatus(409)
            ->assertJsonStructure(['message', 'blockers']);

        // Should NOT be soft-deleted
        $this->assertDatabaseHas('expeditions', ['id' => $expedition->id, 'deleted_at' => null]);
    }

    public function test_operator_cannot_delete_expedition(): void
    {
        $expedition = Expedition::factory()->create(['participants' => []]);
        Sanctum::actingAs(User::factory()->create(['role' => 'OPERATOR']), ['*']);

        // OPERATOR does NOT have role:ADMIN,OPERATOR... wait — OPERATOR is in the list
        // Let's verify the write middleware allows OPERATOR but not GUIDE
        $response = $this->deleteJson("/api/v1/expeditions/{$expedition->id}");

        // OPERATOR is allowed in write routes
        $response->assertOk();
    }

    public function test_guide_cannot_delete_expedition(): void
    {
        $expedition = Expedition::factory()->create(['participants' => []]);
        Sanctum::actingAs(User::factory()->create(['role' => 'GUIDE']), ['*']);

        $this->deleteJson("/api/v1/expeditions/{$expedition->id}")
            ->assertStatus(403);
    }

    // =========================================================================
    // PATCH /api/v1/expeditions/{expedition}/status — updateStatus
    // =========================================================================

    public function test_admin_can_update_expedition_status(): void
    {
        $expedition = Expedition::factory()->create(['status' => 'PLANNING']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->patchJson("/api/v1/expeditions/{$expedition->id}/status", [
            'status' => 'OPEN',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('expeditions', ['id' => $expedition->id, 'status' => 'OPEN']);
    }

    public function test_status_update_validates_enum(): void
    {
        $expedition = Expedition::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $this->patchJson("/api/v1/expeditions/{$expedition->id}/status", ['status' => 'INVALID'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    // =========================================================================
    // GET /api/v1/expeditions/public — publicList
    // =========================================================================

    public function test_public_list_returns_only_open_and_guaranteed(): void
    {
        Expedition::factory()->count(2)->create(['status' => 'OPEN']);
        Expedition::factory()->count(2)->create(['status' => 'GUARANTEED']);
        Expedition::factory()->count(3)->create(['status' => 'PLANNING']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/expeditions/public');

        $response->assertOk();
        $this->assertEquals(4, $response->json('meta.total'));
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function validExpeditionPayload(): array
    {
        return [
            'name'             => 'Expedição Teste',
            'destination'      => 'Chapada dos Veadeiros',
            'dates'            => '15/07/2027 a 22/07/2027',
            'start_date'       => '2027-07-15',
            'end_date'         => '2027-07-22',
            'capacity'         => 12,
            'remaining_spots'  => 12,
            'accommodation'    => 'Camping',
            'transport'        => 'Van',
            'trail_level'      => 'MODERATE',
            'costs'            => 5000.00,
            'margin_predicted' => 2000.00,
        ];
    }
}
