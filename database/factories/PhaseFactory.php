<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PhaseType;
use App\Models\EliminationConfiguration;
use App\Models\Phase;
use App\Models\Tournament;
use App\Services\Generators\EliminationRoundsGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Phase>
 */
class PhaseFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => PhaseType::ELIMINATION,
            'configuration' => EliminationConfiguration::fromArray(['numberOfContestants' => 2]),
        ];
    }

    /** @param array<string, mixed> $configuration */
    public function withConfiguration(array $configuration): static
    {
        return $this->afterMaking(function (Phase $phase) use ($configuration): void {
            $phase->configuration = EliminationConfiguration::fromArray($configuration);
        });
    }

    public function withRounds(): static
    {
        return $this->afterCreating(function (Phase $phase): void {
            new EliminationRoundsGenerator()->generate($phase);
        });
    }

    public function forTournament(Tournament $tournament): static
    {
        return $this->afterMaking(function (Phase $phase) use ($tournament): void {
            $phase->tournament_id = $tournament->id;
        });
    }
}
