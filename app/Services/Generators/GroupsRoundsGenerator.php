<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Models\GroupPhase;
use App\Models\Phase;

/**
 * @implements Generator<GroupPhase>
 */
class GroupsRoundsGenerator implements Generator
{
    public function supports(Phase $phase): bool
    {
        return $phase->isGroup();
    }

    public function generate(Phase $phase): void
    {
        $phase->rounds()->create([
            'stage' => 'Round robin 1',
        ]);
    }
}
