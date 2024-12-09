<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\TournamentStatus;
use App\Events\PhaseCreated;

class PhaseCreatedListener
{
    public function handle(PhaseCreated $event): void
    {
        $tournament = $event->tournament;

        $tournament->update(['status' => TournamentStatus::READY_TO_START]);
    }
}
