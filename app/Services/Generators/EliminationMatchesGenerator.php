<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Enums\EliminationRoundStage;
use App\Models\EliminationPhase;
use App\Models\Phase;
use App\Models\Round;

/**
 * @implements Generator<EliminationPhase>
 */
class EliminationMatchesGenerator implements Generator
{
    public function supports(Phase $phase): bool
    {
        return $phase->isElimination();
    }

    public function generate(Phase $phase): void
    {
        $contestants = $phase->tournament->contestants()->shuffle();
        $rounds = $phase->rounds
            ->sortByDesc(fn (Round $round) => EliminationRoundStage::from($round->stage)->getMatchCount())
            ->values();

        foreach ($rounds as $key => $round) {
            $matchesCount = EliminationRoundStage::from($round->stage)->getMatchCount();

            for ($i = 0; $i < $matchesCount; ++$i) {
                $match = $round->matches()->create([
                    'index' => $i,
                ]);

                if (0 === $key) { // First round
                    $match->addContestants($contestants->splice(0, 2));
                }
            }
        }
    }
}
