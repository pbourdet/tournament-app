<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Jobs\GenerateTeams;
use App\Livewire\Component;
use App\Livewire\Tournament\WithTournamentLock;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Computed;

class Teams extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;

    public bool $organizerMode = true;

    public function render(): View
    {
        $this->tournament->load(['teams.members', 'teams.tournament']);

        return view('livewire.tournament.organize.teams');
    }

    /** @return array<string, string> */
    #[Computed]
    public function selectablePlayers(): array
    {
        /* @phpstan-ignore-next-line */
        return $this->tournament->players()->withoutTeamsInTournament($this->tournament)->pluck('username', 'id')->toArray();
    }

    public function generate(): void
    {
        $this->checkLock();
        $this->tournament->load(['teams.members', 'teams.tournament']);

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        GenerateTeams::dispatch($this->tournament);
        $this->locked = true;
        $this->toast(__('Teams generation in progress...'));
    }

    public function addMember(Team $team, string $playerId): void
    {
        $this->checkLock();

        if ($team->members->count() >= $team->tournament->team_size) {
            abort(403);
        }

        if (!$this->tournament->players()->withoutTeamsInTournament($this->tournament)->where('id', $playerId)->exists()) {
            abort(403);
        }

        $team->members()->attach($playerId);
        $this->toastSuccess(__('Player added to team !'));
    }

    public function removeMember(Team $team, User $user): void
    {
        $this->checkLock();

        $team->members()->detach($user);
        $this->toastSuccess(__('Player removed from team !'));
    }
}
