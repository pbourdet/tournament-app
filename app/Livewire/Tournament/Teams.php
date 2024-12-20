<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Jobs\GenerateTeams;
use App\Livewire\Component;
use App\Livewire\Forms\CreateTeamForm;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Modelable;

class Teams extends Component
{
    use WithTournamentLock;

    #[Modelable]
    public Tournament $tournament;

    public CreateTeamForm $createForm;

    public function mount(Tournament $tournament): void
    {
        $this->createForm->tournament = $tournament;
        $this->tournament = $tournament;
    }

    public function render(): View
    {
        return view('livewire.tournament.teams', [
            'tournament' => $this->tournament->load(['teams.members']),
            'selectablePlayers' => $this->tournament->players()->withoutTeams()->pluck('username', 'id')->toArray(),
        ]);
    }

    public function generate(): void
    {
        $this->checkLock();

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        GenerateTeams::dispatch($this->tournament);
        $this->locked = true;
        $this->toast(__('Teams generation in progress...'));
    }

    public function delete(Team $team): void
    {
        $this->checkLock();

        $team->delete();
        $this->toast(__('Team :name deleted !', ['name' => $team->name]), variant: ToastType::SUCCESS->value);
    }

    public function create(): void
    {
        $this->checkLock();

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
}
