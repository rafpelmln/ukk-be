<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadershipStructureRole>
 */
class LeadershipStructureRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'person_name' => $this->faker->name(),
            'photo_path' => 'foto/leadership-structures/' . $this->faker->uuid() . '.jpg',
            'display_order' => $this->faker->numberBetween(1, 5),
        ];
    }
}
