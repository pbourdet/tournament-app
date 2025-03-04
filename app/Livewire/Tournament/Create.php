<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\CreateForm;
use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;

class Create extends Component
{
    public CreateForm $form;

    #[Locked]
    public int $currentStep = 1;

    /** @var array<int, string[]> */
    private array $steps = [
        1 => ['name', 'description'],
        2 => ['numberOfPlayers'],
        3 => ['teamBased', 'teamSize'],
        4 => ['joinTournament'],
    ];

    public function mount(): void
    {
        if (!Gate::allows('create', Tournament::class)) {
            session()->flash('toast', ['text' => __('You cannot create more tournaments'), 'variant' => ToastType::DANGER->value]);
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function next(): void
    {
        if ($this->currentStep >= count($this->steps)) {
            return;
        }

        $this->resetErrorBag();

        foreach ($this->steps[$this->currentStep] as $field) {
            $this->form->validateOnly($field);
        }

        ++$this->currentStep;
    }

    public function previous(): void
    {
        if ($this->currentStep <= 1) {
            return;
        }

        --$this->currentStep;
    }

    public function render(): View
    {
        return view('livewire.tournament.create', [
            'lastStep' => count($this->steps),
            'firstStep' => 1,
        ]);
    }

    public function save(): void
    {
        $this->authorize('create', Tournament::class);

        $this->form->validate();

        $tournament = $this->user()->managedTournaments()->create([
            'name' => $this->form->name,
            'description' => $this->form->description,
            'number_of_players' => $this->form->numberOfPlayers,
            'team_based' => $this->form->teamBased,
            'team_size' => $this->form->teamBased ? $this->form->teamSize : null,
        ]);

        $tournament->createInvitation();
        $tournament->createTeams();

        if ($this->form->joinTournament) {
            $tournament->addPlayer($this->user());
        }

        $this->toastSuccess(__('Tournament :name created !', ['name' => $tournament->name]));
        $this->redirectRoute('tournaments.show', ['tournament' => $tournament], navigate: true);
    }
}
