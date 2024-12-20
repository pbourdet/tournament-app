<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EliminationPhase;
use App\Models\Round;

class EliminationMatchesGenerator
{
    public function generate(EliminationPhase $phase): void
    {
        $contestants = $phase->tournament->contestants()->shuffle();
        $rounds = $phase->rounds
            ->sortByDesc(fn (Round $round) => $round->stage->getMatchCount())
            ->values();

        foreach ($rounds as $key => $round) {
            $matchesCount = $round->stage->getMatchCount();

            for ($i = 0; $i < $matchesCount; ++$i) {
                $match = $round->matches()->create([
                    'tournament_id' => $phase->tournament->id,
                    'index' => $i + 1,
                ]);

                if (0 === $key) { // First round
                    $match->contestants()->attach($contestants->splice(0, 2));
                }
            }
        }
    }
}
