<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Livewire\Component;
use App\Models\Tournament;

class General extends Component
{
    public Tournament $tournament;

    public function mount(): void
    {
        $this->tournament->load('teams.tournament', 'teams.members', 'groupPhase.groups.contestants');
    }

    public function start(): void
    {
        $this->tournament->save();
    }
}
