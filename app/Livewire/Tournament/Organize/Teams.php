<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\TournamentUpdated;
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
        return $this->tournament->playersWithoutTeams->pluck('username', 'id')->toArray();
    }

    public function generate(): void
    {
        $this->checkLock();

        $this->tournament->load(['teams.members', 'teams.tournament']);
        $this->authorize('generateTeams', $this->tournament);

        dispatch(new GenerateTeams($this->tournament));
        $this->locked = true;
        $this->toast(__('Teams generation in progress...'));
    }

    public function addMember(Team $team, string $playerId): void
    {
        $this->checkLock();
        $this->authorize('addMember', [$team, $playerId]);

        $team->addMember($playerId);
        event(new TournamentUpdated($this->tournament, broadcastToCurrentUser: false));
        $this->toastSuccess(__('Player added to team !'));
    }

    public function removeMember(Team $team, User $user): void
    {
        $this->checkLock();
        $this->authorize('removeMember', [$team, $user]);

        $team->members()->detach($user);
        event(new TournamentUpdated($this->tournament, broadcastToCurrentUser: false));
        $this->toastSuccess(__('Player removed from team !'));
    }
}
