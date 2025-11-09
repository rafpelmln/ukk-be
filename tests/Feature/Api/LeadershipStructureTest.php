<?php

namespace Tests\Feature\Api;

use App\Models\LeadershipStructure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadershipStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_active_period_with_supporting_leaders(): void
    {
        $active = LeadershipStructure::factory()->active()->create([
            'period_label' => 'Periode 2025 - Sekarang',
            'period_year' => '2025 - Sekarang',
        ]);

        $activeRole = $active->roles()->create([
            'title' => 'Ketua 1',
            'person_name' => 'Aninda',
            'photo_path' => 'foto/leadership-structures/aninda.jpg',
            'display_order' => 1,
        ]);

        $inactive = LeadershipStructure::factory()->create([
            'period_label' => 'Periode 2023 - 2024',
            'period_year' => '2023 - 2024',
            'general_leader_name' => 'Ketua Arsip',
        ]);

        $response = $this->getJson(route('api.leadership-structures.index'));

        $response->assertOk()
            ->assertJsonPath('current.period_label', $active->period_label)
            ->assertJsonPath('current.ketua_umum.name', $active->general_leader_name)
            ->assertJsonPath('current.roles.0.title', $activeRole->title)
            ->assertJsonPath('current.roles.0.person_name', $activeRole->person_name)
            ->assertJsonPath('previous.0.period_label', $inactive->period_label)
            ->assertJsonPath('previous.0.ketua.name', 'Ketua Arsip');

        $this->assertArrayNotHasKey('roles', $response->json('previous.0'));
    }

    public function test_current_is_null_when_no_active_period(): void
    {
        LeadershipStructure::factory()->count(2)->create();

        $response = $this->getJson(route('api.leadership-structures.index'));

        $response->assertOk()
            ->assertJson(['current' => null])
            ->assertJsonCount(2, 'previous');
    }
}
