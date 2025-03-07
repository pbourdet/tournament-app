<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\PhaseCreated;
use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateEliminationForm;
use App\Models\EliminationPhase;
use App\Models\Tournament;
use Illuminate\View\View;
use Livewire\Attributes\Url;

class Elimination extends Component
{
    public Tournament $tournament;

    #[Url]
    public string $tab = 'settings';

    public CreateEliminationForm $form;

    public function render(): View
    {
        $this->tournament->load([
            'eliminationPhase.rounds.matches.contestants',
            'eliminationPhase.rounds.matches.results',
        ]);

        return view('livewire.tournament.organize.elimination', [
            'eliminationPhase' => $this->tournament->eliminationPhase,
        ]);
    }

    public function create(): void
    {
        $this->authorize('create', [EliminationPhase::class, $this->tournament]);

        $this->form->validate();

        $this->tournament->eliminationPhase()->create([
            'number_of_contestants' => $this->form->numberOfContestants,
        ]);

        event(new PhaseCreated($this->tournament));
        event(new TournamentUpdated($this->tournament));
        $this->toastSuccess(__('Elimination phase created successfully !'));
    }
}
