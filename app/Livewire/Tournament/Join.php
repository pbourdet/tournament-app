<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use App\Notifications\TournamentFull;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

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
            $tournament->organizer->notify((new TournamentFull($tournament))->afterCommit());
        }

        session()->flash(ToastType::SUCCESS->value, __('You joined tournament :name', ['name' => $tournament->name]));
        $this->redirect(route('tournaments.show', ['tournament' => $tournament]), navigate: true);
    }

    public function find(): void
    {
        $tournamentInvitation = TournamentInvitation::where('code', mb_strtoupper($this->tournamentCode))->first();

        $this->tournament = $tournamentInvitation?->tournament;
        $this->showResponse = true;
    }
}
