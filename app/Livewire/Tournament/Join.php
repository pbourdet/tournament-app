<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\TournamentFull;
use App\Livewire\Component;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
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
        $this->authorize('join', $tournament);

        $tournament->addPlayer($this->user());

        if ($tournament->isFull()) {
            event(new TournamentFull($tournament));
        }

        $this->toastSuccess(__('You joined tournament :name', ['name' => $tournament->name]));
        $this->redirectRoute('tournaments.show', ['tournament' => $tournament], navigate: true);
    }

    public function find(): void
    {
        $tournamentInvitation = TournamentInvitation::notExpired()->firstWhere('code', mb_strtoupper($this->tournamentCode));

        $this->tournament = $tournamentInvitation?->tournament;
        $this->showResponse = true;
    }
}
