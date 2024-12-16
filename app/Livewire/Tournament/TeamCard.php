<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;

class TeamCard extends Component
{
    public string $newName = '';

    #[Locked]
    public bool $generationInProgress = false;

    public Team $team;

    public Tournament $tournament;

    public function mount(Team $team, Tournament $tournament, bool $generationInProgress): void
    {
        $this->team = $team;
        $this->tournament = $tournament;
        $this->newName = $team->name;
        $this->generationInProgress = $generationInProgress;
    }

    public function update(): void
    {
        $this->authorize('manage', $this->tournament);
        $this->validate([
            'newName' => ['required', 'min:2', 'max:50', Rule::unique('teams', 'name')->where('tournament_id', $this->tournament->id)->ignore($this->team->id)],
        ]);

        $this->team->update(['name' => $this->newName]);
        $this->toast(__('Team :name updated !', ['name' => $this->team->name]), variant: ToastType::SUCCESS->value);
    }
}
