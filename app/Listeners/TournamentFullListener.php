<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TournamentStatus;
use App\Events\TournamentFull;

class TournamentFullListener
{
    public function handle(TournamentFull $event): void
    {
        $tournament = $event->tournament;
        $newStatus = $tournament->getPhases()->isNotEmpty() ? TournamentStatus::READY_TO_START : TournamentStatus::SETUP_IN_PROGRESS;

        $tournament->update(['status' => $newStatus]);
        $tournament->organizer->notify(new \App\Notifications\TournamentFull($tournament));
    }
}
