<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EliminationPhase;
use App\Models\Tournament;
use App\Services\EliminationRoundsGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EliminationPhase>
 */
class EliminationPhaseFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'number_of_contestants' => 2,
        ];
    }

    public function withRounds(): static
    {
        return $this->afterCreating(function (EliminationPhase $phase): void {
            new EliminationRoundsGenerator()->generate($phase);
        });
    }

    public function forTournament(Tournament $tournament): static
    {
        return $this->afterMaking(function (EliminationPhase $phase) use ($tournament): void {
            $phase->tournament_id = $tournament->id;
        });
    }
}
