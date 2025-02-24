<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TournamentStatus;
use App\Events\PhaseCreated;
use App\Jobs\GenerateTeams;
use App\Jobs\StartTournament;
use App\Models\EliminationPhase;
use App\Models\GroupPhase;
use App\Models\Tournament;
use App\Models\User;
use App\Services\Generators\EliminationMatchesGenerator;
use App\Services\Generators\EliminationRoundsGenerator;
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
            'status' => TournamentStatus::WAITING_FOR_PLAYERS,
        ];
    }

    public function full(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            while (!$tournament->isFull()) {
                $tournament->players()->attach(User::factory()->create());
            }

            $tournament->update(['status' => TournamentStatus::SETUP_IN_PROGRESS]);
        });
    }

    /** @param array<User>|Collection<int, User> $users */
    public function withPlayers(array|Collection $users): static
    {
        return $this->afterCreating(function (Tournament $tournament) use ($users): void {
            $tournament->players()->attach(collect($users)->pluck('id'));
        });
    }

    public function withEliminationPhase(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            EliminationPhase::factory()->forTournament($tournament)->create();
            PhaseCreated::dispatch($tournament);
        });
    }

    public function withGroupPhase(): static
    {
        return $this->afterCreating(function (Tournament $tournament): void {
            GroupPhase::factory()->forTournament($tournament)->create();
            PhaseCreated::dispatch($tournament);
        });
    }

    public function withEliminationPhaseAndMatches(): static
    {
        return $this->withEliminationPhase()->afterCreating(function (Tournament $tournament): void {
            new EliminationRoundsGenerator()->generate($tournament->eliminationPhase);
            new EliminationMatchesGenerator()->generate($tournament->eliminationPhase);
        });
    }

    public function started(): static
    {
        return $this->full()->afterCreating(function (Tournament $tournament): void {
            EliminationPhase::factory()->forTournament($tournament)->create();
            PhaseCreated::dispatch($tournament);
            StartTournament::dispatchSync($tournament);
            $tournament->refresh();
        });
    }

    public function withFullTeams(): static
    {
        return $this->teamBased()->full()->afterCreating(function (Tournament $tournament): void {
            new GenerateTeams($tournament)->handle();
            $tournament->refresh();
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
        return $this->afterCreating(function (Tournament $tournament) use ($teamSize): void {
            $tournament->team_based = true;
            $tournament->team_size = $teamSize;
            $tournament->createTeams();
            $tournament->save();
        });
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Tournament $tournament): void {
            if (null === $tournament->organizer_id) {
                $tournament->organizer_id = User::factory()->create()->id;
            }
        })->afterCreating(function (Tournament $tournament): void {
            $tournament->createInvitation();
        })
        ;
    }
}
