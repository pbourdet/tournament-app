<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Jobs\StartTournament;
use App\Livewire\Component;
use App\Livewire\Tournament\WithTournamentLock;
use App\Models\Tournament;

class General extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public function mount(): void
    {
        $this->tournament->load('teams.tournament', 'teams.members', 'groupPhase.groups.contestants');
    }

    public function start(): void
    {
        $this->checkLock();
        $this->authorize('start', $this->tournament);

        dispatch(new StartTournament($this->tournament));
        $this->locked = true;
        $this->toast(__('The tournament will soon start ! Matches are being generated and players will be notified.'));
    }
}
