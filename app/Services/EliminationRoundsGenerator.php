<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoundStage;
use App\Models\EliminationPhase;

class EliminationRoundsGenerator
{
    public function generate(EliminationPhase $phase, int $numberOfContestants): void
    {
        $rounds = RoundStage::getRoundsForContestants($numberOfContestants);

        foreach ($rounds as $round) {
            $phase->rounds()->create([
                'stage' => $round->value,
            ]);
        }
    }
}
