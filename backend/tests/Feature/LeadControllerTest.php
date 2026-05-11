<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * LeadController feature tests.
 *
 * Covers: CRUD completo, busca/filtros, paginação, gerenciamento de notas.
 */
class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // GET /api/v1/leads — index
    // =========================================================================

    public function test_authenticated_user_can_list_leads(): void
    {
        Lead::factory()->count(5)->create();
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/leads');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                'links',
            ]);
    }

    public function test_unauthenticated_cannot_list_leads(): void
    {
        $this->getJson('/api/v1/leads')->assertStatus(401);
    }

    public function test_index_filters_by_status(): void
    {
        Lead::factory()->count(3)->create(['status' => 'NEW']);
        Lead::factory()->count(2)->create(['status' => 'PAID']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/leads?status=NEW');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_index_supports_search(): void
    {
        Lead::factory()->create(['name' => 'João da Silva Adventurous']);
        Lead::factory()->create(['name' => 'Maria Outback']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson('/api/v1/leads?search=João');

        $response->assertOk();
        // At least the João record should be returned
        $names = collect($response->json('data'))->pluck('name');
        $this->assertTrue($names->contains('João da Silva Adventurous'));
    }

    // =========================================================================
    // GET /api/v1/leads/{lead} — show
    // =========================================================================

    public function test_can_view_single_lead(): void
    {
        $lead = Lead::factory()->create(['name' => 'Fernanda Costa']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->getJson("/api/v1/leads/{$lead->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Fernanda Costa']);
    }

    public function test_show_returns_404_for_missing_lead(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $this->getJson('/api/v1/leads/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    }

    // =========================================================================
    // POST /api/v1/leads — store
    // =========================================================================

    public function test_admin_can_create_lead(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->postJson('/api/v1/leads', $this->validLeadPayload());

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'whatsapp', 'status']);

        $this->assertDatabaseHas('leads', ['name' => 'Novo Lead Teste']);
    }

    public function test_operator_can_create_lead(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'OPERATOR']), ['*']);

        $this->postJson('/api/v1/leads', $this->validLeadPayload())
            ->assertStatus(201);
    }

    public function test_guide_cannot_create_lead(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'GUIDE']), ['*']);

        $this->postJson('/api/v1/leads', $this->validLeadPayload())
            ->assertStatus(403);
    }

    public function test_store_validates_required_fields(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->postJson('/api/v1/leads', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'whatsapp', 'source', 'destination']);
    }

    public function test_store_validates_status_enum(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);
        $payload = array_merge($this->validLeadPayload(), ['status' => 'INVALID_STATUS']);

        $this->postJson('/api/v1/leads', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    // =========================================================================
    // PUT /api/v1/leads/{lead} — update
    // =========================================================================

    public function test_admin_can_update_lead(): void
    {
        $lead = Lead::factory()->create(['name' => 'Old Name']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->putJson("/api/v1/leads/{$lead->id}", ['name' => 'New Name']);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'name' => 'New Name']);
    }

    // =========================================================================
    // DELETE /api/v1/leads/{lead} — destroy
    // =========================================================================

    public function test_admin_can_delete_lead(): void
    {
        $lead = Lead::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->deleteJson("/api/v1/leads/{$lead->id}");

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Lead deleted successfully']);

        $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
    }

    public function test_guide_cannot_delete_lead(): void
    {
        $lead = Lead::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role' => 'GUIDE']), ['*']);

        $this->deleteJson("/api/v1/leads/{$lead->id}")
            ->assertStatus(403);
    }

    // =========================================================================
    // PATCH /api/v1/leads/{lead}/status — updateStatus
    // =========================================================================

    public function test_can_update_lead_status(): void
    {
        $lead = Lead::factory()->create(['status' => 'NEW']);
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $response = $this->patchJson("/api/v1/leads/{$lead->id}/status", [
            'status' => 'CONTACTED',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'CONTACTED']);
    }

    public function test_status_update_rejects_invalid_value(): void
    {
        $lead = Lead::factory()->create();
        Sanctum::actingAs(User::factory()->admin()->create(), ['*']);

        $this->patchJson("/api/v1/leads/{$lead->id}/status", ['status' => 'BOGUS'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    // =========================================================================
    // POST /api/v1/leads/{lead}/notes — addNote
    // =========================================================================

    public function test_user_can_add_note_to_lead(): void
    {
        $user = User::factory()->admin()->create();
        $lead = Lead::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson("/api/v1/leads/{$lead->id}/notes", [
            'body' => 'Cliente demonstrou interesse em expedição para a Patagônia.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'body', 'author']);

        $this->assertDatabaseHas('lead_notes', [
            'lead_id' => $lead->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_note_body_is_required(): void
    {
        $user = User::factory()->admin()->create();
        $lead = Lead::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $this->postJson("/api/v1/leads/{$lead->id}/notes", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    // =========================================================================
    // PUT /api/v1/leads/{lead}/notes/{note} — editNote
    // =========================================================================

    public function test_user_can_edit_own_note(): void
    {
        $user = User::factory()->admin()->create();
        $lead = Lead::factory()->create();
        $note = LeadNote::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'body'    => 'Original body',
        ]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->putJson("/api/v1/leads/{$lead->id}/notes/{$note->id}", [
            'body' => 'Updated body',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['body' => 'Updated body']);
    }

    public function test_user_cannot_edit_another_users_note(): void
    {
        $owner   = User::factory()->admin()->create();
        $intruder = User::factory()->create(['role' => 'OPERATOR']);
        $lead    = Lead::factory()->create();
        $note    = LeadNote::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $owner->id,
        ]);
        Sanctum::actingAs($intruder, ['*']);

        $this->putJson("/api/v1/leads/{$lead->id}/notes/{$note->id}", ['body' => 'Hijacked!'])
            ->assertStatus(403);
    }

    public function test_admin_can_edit_any_note(): void
    {
        $owner = User::factory()->create(['role' => 'OPERATOR']);
        $admin = User::factory()->admin()->create();
        $lead  = Lead::factory()->create();
        $note  = LeadNote::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $owner->id,
            'body'    => 'Original',
        ]);
        Sanctum::actingAs($admin, ['*']);

        $this->putJson("/api/v1/leads/{$lead->id}/notes/{$note->id}", ['body' => 'Admin edited'])
            ->assertOk()
            ->assertJsonFragment(['body' => 'Admin edited']);
    }

    // =========================================================================
    // DELETE /api/v1/leads/{lead}/notes/{note} — deleteNote
    // =========================================================================

    public function test_user_can_delete_own_note(): void
    {
        $user = User::factory()->admin()->create();
        $lead = Lead::factory()->create();
        $note = LeadNote::factory()->create(['lead_id' => $lead->id, 'user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']);

        $this->deleteJson("/api/v1/leads/{$lead->id}/notes/{$note->id}")
            ->assertOk();

        $this->assertDatabaseMissing('lead_notes', ['id' => $note->id]);
    }

    public function test_user_cannot_delete_another_users_note(): void
    {
        $owner    = User::factory()->admin()->create();
        $intruder = User::factory()->create(['role' => 'OPERATOR']);
        $lead     = Lead::factory()->create();
        $note     = LeadNote::factory()->create(['lead_id' => $lead->id, 'user_id' => $owner->id]);
        Sanctum::actingAs($intruder, ['*']);

        $this->deleteJson("/api/v1/leads/{$lead->id}/notes/{$note->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('lead_notes', ['id' => $note->id]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function validLeadPayload(): array
    {
        return [
            'name'             => 'Novo Lead Teste',
            'whatsapp'         => '+55 11 99999-1234',
            'source'           => 'INSTAGRAM',
            'interest'         => 'Expedição para a Chapada',
            'destination'      => 'Chapada dos Veadeiros',
            'date_desired'     => '2027-09-01',
            'people_count'     => 2,
            'estimated_ticket' => 3500.00,
            'last_contact'     => '2026-05-01',
            'next_follow_up'   => '2026-05-15',
        ];
    }
}
