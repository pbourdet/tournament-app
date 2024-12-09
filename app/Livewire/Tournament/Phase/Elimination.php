<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Enums\ToastType;
use App\Events\PhaseCreated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateEliminationForm;
use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class Elimination extends Component
{
    public Tournament $tournament;

    public CreateEliminationForm $form;

    public function render(): View
    {
        $this->tournament->load('eliminationPhase.rounds.matches');

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
            'number_of_contestants' => $this->form->numberOfContestants,
        ]);

        PhaseCreated::dispatch($this->tournament);
        $this->toast(ToastType::SUCCESS, __('Elimination phase created successfully !'));
    }
}
