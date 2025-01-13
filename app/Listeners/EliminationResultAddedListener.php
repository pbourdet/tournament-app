<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TournamentStatus;
use App\Events\ResultAdded;

class EliminationResultAddedListener
{
    public function handle(ResultAdded $event): void
    {
        $match = $event->match;
        $phase = $match->round->phase;

        if (!$phase->isElimination()) {
            return;
        }

        $nextMatch = $phase->getNextMatchOf($match);

        if (null === $nextMatch) {
            $tournament = $phase->tournament;
            $tournament->update(['status' => TournamentStatus::FINISHED]);

            return;
        }

        $nextMatch->contestants()->whereIn('contestant_id', $match->contestants->map->contestant_id)->delete();
        $nextMatch->addContestants($match->winners());
    }
}
