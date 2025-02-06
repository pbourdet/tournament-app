<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Events\PhaseCreated;
use App\Models\GroupPhase;

/**
 * @extends PhaseFactory<GroupPhase>
 */
class GroupPhaseFactory extends PhaseFactory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'number_of_groups' => 2,
            'qualifying_per_group' => 1,
        ];
    }

    public function withGroups(): static
    {
        return $this->afterCreating(function (GroupPhase $phase): void {
            PhaseCreated::dispatch($phase->tournament);
        });
    }
}
