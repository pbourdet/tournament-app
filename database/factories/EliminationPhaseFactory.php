<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EliminationPhase;
use App\Models\Phase;
use App\Services\Generators\EliminationMatchesGenerator;
use App\Services\Generators\EliminationRoundsGenerator;

/**
 * @extends PhaseFactory<EliminationPhase>
 */
class EliminationPhaseFactory extends PhaseFactory
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

    public function withMatches(): static
    {
        return $this->afterCreating(function (Phase $phase): void {
            new EliminationRoundsGenerator()->generate($phase);
            new EliminationMatchesGenerator()->generate($phase);
        });
    }
}
