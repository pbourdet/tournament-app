<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tournament> */
class TournamentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->words(asText: true),
            'description' => fake()->text(),
            'number_of_players' => fake()->numberBetween(1, 5) * 2,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Tournament $tournament): void {
            if (null === $tournament->organizer_id) {
                $tournament->organizer_id = User::factory()->create()->id;
            }
        });
    }
}
