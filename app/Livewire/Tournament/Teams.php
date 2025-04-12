<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\View\View;

class Teams extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public function render(): View
    {
        return view('livewire.tournament.teams', [
            'selectablePlayers' => [],
        ]);
    }
}
