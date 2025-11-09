<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadershipStructure>
 */
class LeadershipStructureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2010, 2035);
        $endYear = $startYear + 1;
        $label = "Periode {$startYear} - {$endYear}";

        return [
            'period_label' => $label,
            'period_year' => "{$startYear}-{$endYear}",
            'is_active' => false,
            'general_leader_name' => $this->faker->name(),
            'general_leader_photo_path' => 'foto/leadership-structures/' . Str::uuid() . '.jpg',
        ];
    }

    public function active(): self
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($structure) {
            $structure->roles()->createMany([
                [
                    'title' => 'Ketua 1',
                    'person_name' => $this->faker->name(),
                    'photo_path' => 'foto/leadership-structures/' . Str::uuid() . '.jpg',
                    'display_order' => 1,
                ],
                [
                    'title' => 'Ketua 2',
                    'person_name' => $this->faker->name(),
                    'photo_path' => 'foto/leadership-structures/' . Str::uuid() . '.jpg',
                    'display_order' => 2,
                ],
            ]);
        });
    }
}
