<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Qualification extends Component
{
    public Tournament $tournament;

    /** @var array<string, string> */
    public array $selectableContestants = [];

    #[Locked]
    public bool $organizerMode = false;

    public function render(): View
    {
        $this->tournament->load('groupPhase.groups.contestants');

        return view('livewire.tournament.phase.qualification');
    }
}
