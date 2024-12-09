<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EliminationPhase;

class EliminationMatchesGenerator
{
    public function generate(EliminationPhase $phase): void
    {
        foreach ($phase->rounds as $round) {
            $matchesCount = $round->stage->getMatchCount();

            for ($i = 0; $i < $matchesCount; ++$i) {
                $round->matches()->create([
                    'tournament_id' => $phase->tournament->id,
                    'index' => $i + 1,
                ]);
            }
        }
    }
}
