<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Models\Contestant;
use App\Models\GroupPhase;
use App\Models\Phase;

/**
 * @implements Generator<GroupPhase>
 */
class GroupsMatchesGenerator implements Generator
{
    public function supports(Phase $phase): bool
    {
        return $phase->isGroup();
    }

    public function generate(Phase $phase): void
    {
        $phase->load('groups.contestants');
        $round = $phase->rounds->firstOrFail(); // Group phase supports only one round for now

        foreach ($phase->groups as $group) {
            $contestants = $group->getContestants();
            $count = $contestants->count();

            for ($i = 0; $i < $count - 1; ++$i) {
                /** @var Contestant $contestant */
                $contestant = $contestants->shift();

                foreach ($contestants as $opponent) {
                    $round->matches()->create([
                        'index' => 0,
                    ])->addContestants([$contestant, $opponent]);
                }
            }
        }
    }
}
