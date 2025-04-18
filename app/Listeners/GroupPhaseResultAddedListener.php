<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ResultAdded;
use App\Jobs\GenerateMatches;

readonly class GroupPhaseResultAddedListener
{
    public function handle(ResultAdded $event): void
    {
        $match = $event->match;
        $phase = $match->round->phase;

        if (!$phase->isGroup() || !$phase->isFinished()) {
            return;
        }

        $tournament = $phase->tournament;

        if (null === $tournament->eliminationPhase) {
            $tournament->finish();

            return;
        }

        dispatch(new GenerateMatches($tournament->eliminationPhase));
    }
}
