<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\TournamentInvitation;
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

    public function full(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            for ($i = 0; $i < $tournament->number_of_players; ++$i) {
                $tournament->players()->attach(User::factory()->create());
            }
        });
    }

    public function withPlayer(User $user): static
    {
        return $this->afterCreating(function (Tournament $tournament) use ($user): void {
            $tournament->players()->attach($user);
        });
    }

    public function withoutInvitation(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            $tournament->invitation?->delete();
        });
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Tournament $tournament): void {
            if (null === $tournament->organizer_id) {
                $tournament->organizer_id = User::factory()->create()->id;
            }
        })->afterCreating(function (Tournament $tournament): void {
            TournamentInvitation::factory()->create(['tournament_id' => $tournament->id]);
        })
        ;
    }
}
