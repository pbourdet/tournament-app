<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Enums\EliminationRoundStage;
use App\Models\EliminationPhase;
use App\Models\Phase;

/**
 * @implements Generator<EliminationPhase>
 */
class EliminationRoundsGenerator implements Generator
{
    public function supports(Phase $phase): bool
    {
        return $phase->isElimination();
    }

    public function generate(Phase $phase): void
    {
        $rounds = EliminationRoundStage::getRoundsForContestants($phase->number_of_contestants);

        foreach ($rounds as $round) {
            $phase->rounds()->create([
                'stage' => $round->value,
            ]);
        }
    }
}
