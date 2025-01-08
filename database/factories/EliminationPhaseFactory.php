<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PhaseType;
use App\Models\EliminationPhase;
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
            'type' => PhaseType::ELIMINATION,
        ];
    }

    /** @param array<string, mixed> $attributes */
    public function withDetails(array $attributes): static
    {
        return $this->afterCreating(function (EliminationPhase $phase) use ($attributes): void {
            $phase->details()->create($attributes);
        });
    }

    public function withRounds(): static
    {
        return $this->afterCreating(function (EliminationPhase $phase): void {
            new EliminationRoundsGenerator()->generate($phase);
        });
    }
}
