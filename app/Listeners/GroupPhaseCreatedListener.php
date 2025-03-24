<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PhaseCreated;

class GroupPhaseCreatedListener
{
    public function handle(PhaseCreated $event): void
    {
        $phase = $event->tournament->groupPhase;

        if (null === $phase || $phase->groups->isNotEmpty()) {
            return;
        }

        $groups = $this->splitInGroups($event->tournament->contestantsCount(), $phase->number_of_groups);

        foreach ($groups as $index => $groupSize) {
            $phase->groups()->create([
                'name' => sprintf('%s %s', __('Group'), $index + 1),
                'size' => $groupSize,
            ]);
        }
    }

    /** @return array<int, int> */
    private function splitInGroups(int $numberOfContestants, int $numberOfGroups): array
    {
        $baseSize = intdiv($numberOfContestants, $numberOfGroups);
        $extra = $numberOfContestants % $numberOfGroups;

        return array_map(
            fn (int $i) => $i < $extra ? $baseSize + 1 : $baseSize,
            range(0, $numberOfGroups - 1)
        );
    }
}
