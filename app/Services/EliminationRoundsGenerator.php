<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoundStage;
use App\Models\EliminationPhase;

class EliminationRoundsGenerator
{
    public function generate(EliminationPhase $phase): void
    {
        $rounds = RoundStage::getRoundsForContestants($phase->number_of_contestants);

        foreach ($rounds as $round) {
            $phase->rounds()->create([
                'stage' => $round->value,
            ]);
        }
    }
}
