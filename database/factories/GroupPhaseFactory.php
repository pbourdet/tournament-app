<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Jobs\GenerateGroups;
use App\Jobs\StartTournament;
use App\Models\GroupPhase;
use App\Services\Generators\GroupsRoundsGenerator;

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

    public function withRounds(): static
    {
        return $this->afterCreating(function (GroupPhase $phase): void {
            new GroupsRoundsGenerator()->generate($phase);
        });
    }

    public function withMatches(): static
    {
        return $this->afterCreating(function (GroupPhase $phase): void {
            dispatch_sync(new GenerateGroups($phase));
            dispatch_sync(new StartTournament($phase->tournament));
        });
    }

    public function withFullGroups(): static
    {
        return $this->afterCreating(function (GroupPhase $phase): void {
            dispatch_sync(new GenerateGroups($phase));
        });
    }
}
