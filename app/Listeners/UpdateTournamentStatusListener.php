<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TournamentUpdated;

class UpdateTournamentStatusListener
{
    public function handle(TournamentUpdated $event): void
    {
        if (!$event->shouldUpdateStatus) return;

        $event->tournament->updateStatus();
    }
}
