<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;

class PlayersTable extends Component
{
    #[Modelable]
    public Tournament $tournament;

    #[Locked, Reactive]
    public bool $generationInProgress = false;

    public function render(): View
    {
        return view('livewire.tournament.players-table', [
            'players' => $this->tournament->players()->orderByRaw('id = ? DESC', [auth()->id()])->get(),
        ]);
    }

    public function removePlayer(string $playerId): void
    {
        $this->authorize('manage', $this->tournament);
        $this->tournament->load('teams.members');

        $player = User::findOrFail($playerId);

        $this->tournament->teams->first(fn ($team) => $team->members->contains($player))?->delete();
        $this->tournament->players()->detach($playerId);
        event(new TournamentUpdated($this->tournament));
    }
}
