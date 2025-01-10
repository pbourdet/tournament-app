<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Enums\RoundStage;
use App\Models\EliminationConfiguration;
use App\Models\Phase;

/**
 * @implements Generator<EliminationConfiguration>
 */
class EliminationRoundsGenerator implements Generator
{
    public function supports(Phase $phase): bool
    {
        return $phase->isElimination();
    }

    public function generate(Phase $phase): void
    {
        $rounds = RoundStage::getRoundsForContestants($phase->configuration->numberOfContestants);

        foreach ($rounds as $round) {
            $phase->rounds()->create([
                'stage' => $round->value,
            ]);
        }
    }
}
