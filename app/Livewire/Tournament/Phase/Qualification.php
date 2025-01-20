<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Enums\PhaseType;
use App\Enums\ToastType;
use App\Events\PhaseCreated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupForm;
use App\Models\GroupConfiguration;
use App\Models\Tournament;
use Illuminate\View\View;

class Qualification extends Component
{
    public Tournament $tournament;

    public PhaseType $type = PhaseType::GROUP;

    public CreateGroupForm $groupForm;

    public function boot(): void
    {
        $this->groupForm->setTournament($this->tournament);
    }

    public function render(): View
    {
        $this->tournament->load('qualificationPhase.groups.contestants');

        return view('livewire.tournament.phase.qualification');
    }

    public function create(): void
    {
        $this->authorize('manage', $this->tournament);
        $this->groupForm->validate();
        $this->tournament->qualificationPhase?->delete();

        $this->tournament->qualificationPhase()->create([
            'type' => $this->type,
            'configuration' => GroupConfiguration::fromArray($this->groupForm->toArray()),
        ]);

        PhaseCreated::dispatch($this->tournament->refresh());
        $this->toast(__('Phase created !'), variant: ToastType::SUCCESS->value);
    }
}
