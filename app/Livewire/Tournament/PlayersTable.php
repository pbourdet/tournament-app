<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;

class PlayersTable extends Component
{
    public Tournament $tournament;

    #[Locked, Reactive]
    public bool $locked = false;

    public function render(): View
    {
        return view('livewire.tournament.players-table', [
            'players' => $this->tournament->players()->orderByRaw('id = ? DESC', [auth()->id()])->get(),
        ]);
    }

    public function remove(User $player): void
    {
        $this->authorize('manage', $this->tournament);

        if ($this->locked) {
            abort(409);
        }

        $this->tournament->load('teams.members');
        $this->tournament->teams->first(fn ($team) => $team->members->contains($player))?->delete();
        $this->tournament->players()->detach($player);

        $this->toastSuccess(__('Player :name removed from tournament.', ['name' => $player->username]));
        event(new TournamentUpdated($this->tournament));
    }
}
