<?php

namespace Tests\Feature\Api;

use App\Models\VisionMissionEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisionMissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_active_visions_and_missions(): void
    {
        VisionMissionEntry::factory()->vision()->create([
            'title' => 'Visi 1',
            'content' => 'Menjadi wadah kolaborasi.',
        ]);

        VisionMissionEntry::factory()->mission()->create([
            'title' => 'Misi 1',
            'content' => "Poin pertama\nPoin kedua",
        ]);

        VisionMissionEntry::factory()->mission()->create([
            'title' => 'Misi Nonaktif',
            'content' => 'Tidak tampil',
            'is_active' => false,
        ]);

        $response = $this->getJson(route('api.vision-mission.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'vision')
            ->assertJsonCount(1, 'mission')
            ->assertJsonPath('vision.0.title', 'Visi 1')
            ->assertJsonPath('mission.0.title', 'Misi 1');
    }
}
