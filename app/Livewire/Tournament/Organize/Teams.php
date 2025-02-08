<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Jobs\GenerateTeams;
use App\Livewire\Component;
use App\Livewire\Forms\CreateTeamForm;
use App\Livewire\Tournament\WithTournamentLock;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;

class Teams extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public string $playerId = '';

    public bool $organizerMode = true;

    public CreateTeamForm $createForm;

    public function mount(Tournament $tournament): void
    {
        $this->createForm->tournament = $tournament;
        $this->tournament = $tournament;
    }

    public function render(): View
    {
        $this->tournament->load('teams.members');

        return view('livewire.tournament.organize.teams', [
            'selectablePlayers' => $this->tournament->players()->withoutTeamsInTournament($this->tournament)->pluck('username', 'id')->toArray(),
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
        $this->toastSuccess(__('Team :name deleted !', ['name' => $team->name]));
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
        $this->toastSuccess(__('Team :name created !', ['name' => $team->name]));
        $this->modal('create-team')->close();
    }

    public function addMember(Team $team, string $playerId): void
    {
        $this->checkLock();

        $team->members()->attach($playerId);
        $this->toastSuccess(__('Player added to team !'));
    }
}
