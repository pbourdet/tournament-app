<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PhaseType;
use App\Events\PhaseCreated;
use App\Models\Phase;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template T of Phase
 *
 * @extends Factory<T>
 */
abstract class PhaseFactory extends Factory
{
    abstract public function withRounds(): static;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'type' => PhaseType::ELIMINATION,
        ];
    }

    public function forTournament(Tournament $tournament): static
    {
        return $this->afterMaking(function (Phase $phase) use ($tournament): void {
            $phase->tournament_id = $tournament->id;
        });
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Phase $phase): void {
            PhaseCreated::dispatch($phase->tournament);
        });
    }
}
