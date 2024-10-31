<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
            'number_of_players' => fake()->numberBetween(2, 5) * 2,
        ];
    }

    public function full(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            while (!$tournament->isFull()) {
                $tournament->players()->attach(User::factory()->create());
            }
        });
    }

    /** @param array<User>|Collection<int, User> $users */
    public function withPlayers(array|Collection $users): static
    {
        return $this->afterCreating(function (Tournament $tournament) use ($users): void {
            $tournament->players()->attach(collect($users)->pluck('id'));
        });
    }

    public function withAllTeams(): static
    {
        return $this->full()->afterCreating(function (Tournament $tournament): void {
            while (!$tournament->hasAllTeams()) {
                $users = User::factory()->count($tournament->team_size)->createMany();
                $team = Team::factory()->withMembers($users)->create(['tournament_id' => $tournament->id]);
                $tournament->teams()->save($team);
            }
        });
    }

    public function withoutInvitation(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            $tournament->invitation?->delete();
        });
    }

    public function teamBased(int $teamSize = 2): static
    {
        return $this->afterMaking(function (Tournament $tournament) use ($teamSize): void {
            $tournament->team_based = true;
            $tournament->team_size = $teamSize;
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
