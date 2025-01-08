<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Enums\PhaseType;
use App\Enums\ToastType;
use App\Events\PhaseCreated;
use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateEliminationForm;
use App\Models\EliminationConfiguration;
use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class Elimination extends Component
{
    public Tournament $tournament;

    public CreateEliminationForm $form;

    public function render(): View
    {
        $this->tournament->load([
            'eliminationPhase.rounds.matches.contestants',
            'eliminationPhase.rounds.matches.results',
        ]);

        return view('livewire.tournament.phase.elimination', [
            'eliminationPhase' => $this->tournament->eliminationPhase,
        ]);
    }

    public function create(): void
    {
        if (!Gate::allows('manage', $this->tournament) || $this->tournament->eliminationPhase()->exists()) {
            abort(403);
        }

        $this->form->validate();

        $this->tournament->eliminationPhase()->create([
            'type' => PhaseType::ELIMINATION,
            'configuration' => EliminationConfiguration::fromArray($this->form->toArray()),
        ]);

        PhaseCreated::dispatch($this->tournament);
        $this->toast(__('Elimination phase created successfully !'), variant: ToastType::SUCCESS->value);
        event(new TournamentUpdated($this->tournament));
    }
}
