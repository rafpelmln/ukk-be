<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VisionMissionEntry>
 */
class VisionMissionEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['vision', 'mission']);

        return [
            'type' => $type,
            'title' => $type === 'vision' ? 'Visi ' . $this->faker->word() : 'Misi ' . $this->faker->word(),
            'content' => $type === 'vision'
                ? $this->faker->paragraph()
                : $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function mission(): self
    {
        return $this->state(fn () => ['type' => 'mission']);
    }

    public function vision(): self
    {
        return $this->state(fn () => ['type' => 'vision']);
    }
}
