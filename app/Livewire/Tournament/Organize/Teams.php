<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Livewire\Component;
use App\Livewire\Forms\CreateTeamForm;
use App\Livewire\Tournament\WithTournamentLock;
use App\Models\Tournament;
use Illuminate\View\View;
use Livewire\Attributes\Computed;

class Teams extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public CreateTeamForm $createForm;

    public function mount(Tournament $tournament): void
    {
        $this->createForm->tournament = $tournament;
        $this->tournament = $tournament;
    }

    public function render(): View
    {
        $this->tournament->load('teams.members');

        return view('livewire.tournament.organize.teams');
    }

    #[Computed]
    /** @return array<string, string> */
    public function selectablePlayers(): array
    {
        return $this->tournament->players()->withoutTeams()->pluck('username', 'id')->toArray();
    }
}
