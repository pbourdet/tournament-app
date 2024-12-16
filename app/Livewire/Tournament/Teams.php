<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Livewire\Forms\CreateTeamForm;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;

class Teams extends Component
{
    #[Modelable]
    public Tournament $tournament;

    public CreateTeamForm $createForm;

    #[Locked]
    #[Reactive]
    public bool $generationInProgress = false;

    public function mount(Tournament $tournament, bool $generationInProgress): void
    {
        $this->createForm->tournament = $tournament;
        $this->tournament = $tournament;
        $this->generationInProgress = $generationInProgress;
    }

    public function render(): View
    {
        return view('livewire.tournament.teams', [
            'tournament' => $this->tournament->load(['teams.members']),
            'selectablePlayers' => $this->tournament->players()->withoutTeams()->pluck('username', 'id')->toArray(),
        ]);
    }

    public function delete(Team $team): void
    {
        $this->checkAuthorization();

        $team->delete();
        $this->toast(__('Team :name deleted !', ['name' => $team->name]), variant: ToastType::SUCCESS->value);
    }

    public function create(): void
    {
        $this->checkAuthorization();

        if (!$this->tournament->team_based || $this->tournament->hasAllTeams()) {
            abort(403);
        }

        $this->createForm->validate();

        $team = Team::create([
            'name' => 0 === strlen(trim($this->createForm->name))
                ? sprintf('%s %s', __('Team'), User::find($this->createForm->members)->firstOrFail()->username)
                : $this->createForm->name,
            'tournament_id' => $this->tournament->id,
        ]);
        $team->members()->attach($this->createForm->members);

        $this->createForm->reset('name', 'members');
        $this->toast(__('Team created !'), variant: ToastType::SUCCESS->value);
        $this->modal('create-team')->close();
    }

    private function checkAuthorization(): void
    {
        $this->authorize('manage', $this->tournament);

        if ($this->generationInProgress) {
            abort(409);
        }
    }
}
