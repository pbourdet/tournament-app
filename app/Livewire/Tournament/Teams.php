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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Teams extends Component
{
    public Tournament $tournament;

    public CreateTeamForm $createForm;

    #[Locked]
    public bool $generationInProgress = false;

    public function mount(Tournament $tournament): void
    {
        $this->createForm->tournament = $tournament;
        $this->tournament = $tournament;
    }

    public function boot(): void
    {
        $this->generationInProgress = $this->generationInProgress();
    }

    public function render(): View
    {
        return view('livewire.tournament.teams', [
            'tournament' => $this->tournament->load(['teams.members']),
            'selectablePlayers' => $this->tournament->players()->withoutTeams()->pluck('name', 'id')->toArray(),
        ]);
    }

    /** @return array<string, string> */
    public function getListeners(): array
    {
        return [
            sprintf('echo-private:App.Models.User.%s,TeamsGenerated', Auth::user()?->id) => 'generationDone',
        ];
    }

    public function generate(): void
    {
        $this->checkAuthorization();

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        GenerateTeams::dispatch($this->tournament);
        $this->generationInProgress = true;
        $this->toast(ToastType::INFO, __('Teams generation in progress'));
    }

    public function generationDone(): void
    {
        $this->generationInProgress = false;
        $this->toast(ToastType::SUCCESS, __('Teams generation done for tournament :tournament !', ['tournament' => $this->tournament->name]));
    }

    public function delete(Team $team): void
    {
        $this->checkAuthorization();

        $team->delete();
        $this->toast(ToastType::SUCCESS, __('Team :name deleted !', ['name' => $team->name]));
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
                ? sprintf('%s %s', __('Team'), User::find($this->createForm->members)->firstOrFail()->name)
                : $this->createForm->name,
            'tournament_id' => $this->tournament->id,
        ]);
        $team->members()->attach($this->createForm->members);

        $this->createForm->reset('name', 'members');
        $this->toast(ToastType::SUCCESS, __('Team :name created !', ['name' => $team->name]));
        $this->dispatch('team-created');
    }

    private function generationInProgress(): bool
    {
        $lock = Cache::lock($this->tournament->getTeamsLockKey(), 60);

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }

    private function checkAuthorization(): void
    {
        $this->authorize('manage', $this->tournament);

        if ($this->generationInProgress) {
            abort(409);
        }
    }
}
