<?php

namespace Tests\Feature;

use App\Models\Expedition;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * [CRÍTICO 2] Feature tests — validates that pagination is enforced
 * and that no single request can return more than 100 records.
 */
class PaginationTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Lead pagination
    // =========================================================================

    public function test_leads_returns_default_25_records(): void
    {
        Lead::factory()->count(50)->create();
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/leads');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page', 'from', 'to'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);

        $this->assertCount(25, $response->json('data'));
        $this->assertEquals(25, $response->json('meta.per_page'));
        $this->assertEquals(50, $response->json('meta.total'));
        $this->assertEquals(2,  $response->json('meta.last_page'));
    }

    public function test_leads_per_page_is_capped_at_100(): void
    {
        Lead::factory()->count(110)->create();
        $this->actingAsAdmin();

        // Request 150 — must be silently capped to 100
        $response = $this->getJson('/api/v1/leads?per_page=150');

        $response->assertOk();
        $this->assertCount(100, $response->json('data'));
        $this->assertEquals(100, $response->json('meta.per_page'));
        $this->assertEquals(110, $response->json('meta.total'));
        $this->assertNotNull($response->json('links.next'), 'Should have a next page');
    }

    public function test_leads_per_page_respects_custom_value_within_limit(): void
    {
        Lead::factory()->count(40)->create();
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/leads?per_page=10');

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(10, $response->json('meta.per_page'));
        $this->assertEquals(4,  $response->json('meta.last_page'));
    }

    public function test_leads_second_page_returns_correct_slice(): void
    {
        Lead::factory()->count(30)->create();
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/leads?per_page=25&page=2');

        $response->assertOk();
        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(2, $response->json('meta.current_page'));
        $this->assertNull($response->json('links.next'));
    }

    // =========================================================================
    // Expedition pagination
    // =========================================================================

    public function test_expeditions_returns_default_25_records(): void
    {
        Expedition::factory()->count(50)->create();
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/expeditions');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);

        $this->assertCount(25, $response->json('data'));
    }

    public function test_expeditions_per_page_is_capped_at_100(): void
    {
        Expedition::factory()->count(110)->create();
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/expeditions?per_page=200');

        $response->assertOk();
        $this->assertCount(100, $response->json('data'));
        $this->assertEquals(100, $response->json('meta.per_page'));
    }

    // =========================================================================
    // Helper
    // =========================================================================

    private function actingAsAdmin(): void
    {
        $user = User::factory()->admin()->create();
        Sanctum::actingAs($user, ['*']);
    }
}
