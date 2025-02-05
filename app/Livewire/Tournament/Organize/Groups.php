<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\PhaseCreated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupsForm;
use App\Models\Tournament;
use Illuminate\View\View;

class Groups extends Component
{
    public Tournament $tournament;

    public CreateGroupsForm $form;

    public function boot(): void
    {
        $this->form->setTournament($this->tournament);
    }

    public function render(): View
    {
        $this->tournament->load('groupPhase.groups.contestants');

        return view('livewire.tournament.organize.groups');
    }

    public function create(): void
    {
        $this->authorize('manage', $this->tournament);
        $this->form->validate();
        $this->tournament->groupPhase?->delete();

        $this->tournament->groupPhase()->create([
            'number_of_groups' => $this->form->numberOfGroups,
            'qualifying_per_group' => $this->form->contestantsQualifying,
        ]);

        PhaseCreated::dispatch($this->tournament->refresh());
        $this->toastSuccess(__('Phase created !'));
    }
}
