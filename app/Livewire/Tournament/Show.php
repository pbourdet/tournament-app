<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Models\Tournament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Show extends Component
{
    public Tournament $tournament;

    public function mount(Tournament $tournament): void
    {
        Gate::authorize('view', $tournament);

        $this->tournament = $tournament;
    }

    public function render(): View
    {
        $this->tournament->load('teams.members');

        return view('livewire.tournament.show');
    }
}
