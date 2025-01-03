<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ResultAdded;
use App\Models\EliminationPhase;

class ResultAddedListener
{
    public function __construct()
    {
    }

    public function handle(ResultAdded $event): void
    {
        $match = $event->match;
        /** @var EliminationPhase $phase */
        $phase = $match->round->phase;
        $nextMatch = $phase->getNextMatchOf($match);

        if (null === $nextMatch) {
            return;
        }

        $nextMatch->contestants()->whereIn('contestant_id', $match->contestants->flatMap->id)->delete();

        $nextMatch->addContestants(collect([$match->winner()]));
    }
}
