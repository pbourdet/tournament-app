<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\PlayerLeft;
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
        $this->authorize('removePlayer', [$this->tournament, $player]);

        if ($this->locked) {
            abort(409);
        }

        event(new PlayerLeft($this->tournament, $player));
        $this->tournament->players()->detach($player);
        event(new TournamentUpdated($this->tournament));

        $this->toastSuccess(__('Player :name removed from tournament.', ['name' => $player->username]));
    }
}
