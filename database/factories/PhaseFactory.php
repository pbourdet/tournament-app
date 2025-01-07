<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Phase;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TPhase of Phase
 *
 * @extends Factory<TPhase>
 */
abstract class PhaseFactory extends Factory
{
    abstract public function withRounds(): static;

    /** @param array<string, mixed> $attributes */
    abstract public function withDetails(array $attributes): static;

    public function forTournament(Tournament $tournament): static
    {
        return $this->afterMaking(function (Phase $phase) use ($tournament): void {
            $phase->tournament_id = $tournament->id;
        });
    }
}
