<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Jobs\StartTournament;
use App\Livewire\Component;
use App\Models\Tournament;

class Overview extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public function start(): void
    {
        $this->checkLock();

        if (!$this->tournament->isReadyToStart()) {
            abort(403);
        }

        StartTournament::dispatch($this->tournament);
        $this->locked = true;
        $this->toast(__('The tournament will soon start ! Matches are being generated and players will be notified.'));
    }
}
