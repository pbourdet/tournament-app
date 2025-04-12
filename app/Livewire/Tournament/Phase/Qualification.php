<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;

class Qualification extends Component
{
    public Tournament $tournament;

    /** @var array<string, string> */
    public array $selectableContestants = [];

    public function render(): View
    {
        return view('livewire.tournament.phase.qualification', [
            'groupPhase' => $this->tournament->groupPhase,
        ]);
    }
}
