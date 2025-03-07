<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PlayerJoined;
use App\Notifications\TournamentFull;

class PlayerJoinedListener
{
    public function handle(PlayerJoined $event): void
    {
        $tournament = $event->tournament;

        if (!$tournament->isFull()) {
            return;
        }

        $tournament->organizer->notify(new TournamentFull($tournament));
    }
}
