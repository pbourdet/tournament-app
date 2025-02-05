<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\View\View;

class Elimination extends Component
{
    public Tournament $tournament;

    public function render(): View
    {
        $this->tournament->load([
            'eliminationPhase.rounds.matches.contestants',
            'eliminationPhase.rounds.matches.results',
        ]);

        return view('livewire.tournament.phase.elimination', [
            'eliminationPhase' => $this->tournament->eliminationPhase,
        ]);
    }
}
