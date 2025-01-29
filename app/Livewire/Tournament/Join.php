<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\TournamentFull;
use App\Livewire\Component;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;

class Join extends Component
{
    public string $tournamentCode = '';

    #[Locked]
    public bool $showResponse = false;

    #[Locked]
    public ?Tournament $tournament = null;

    public function join(Tournament $tournament): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (!Gate::allows('join', $tournament)) {
            abort(403);
        }

        $tournament->players()->attach($user);

        if ($tournament->isFull()) {
            TournamentFull::dispatch($tournament);
        }

        $this->toastSuccess(__('You joined tournament :name', ['name' => $tournament->name]));
        $this->redirectRoute('tournaments.show', ['tournament' => $tournament], navigate: true);
    }

    public function find(): void
    {
        $tournamentInvitation = TournamentInvitation::notExpired()->where('code', mb_strtoupper($this->tournamentCode))->first();

        $this->tournament = $tournamentInvitation?->tournament;
        $this->showResponse = true;
    }
}
