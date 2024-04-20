<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
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
        ];
    }
}