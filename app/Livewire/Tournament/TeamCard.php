<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;

class TeamCard extends Component
{
    public string $newName = '';

    #[Locked]
    public bool $locked = false;

    public Team $team;

    public Tournament $tournament;

    /** @var array<string, string> */
    public array $selectablePlayers = [];

    public function mount(): void
    {
        $this->team->load('tournament');
        $this->newName = $this->team->name;
    }

    public function update(): void
    {
        $this->authorize('updateName', $this->team);

        $this->validate([
            'newName' => ['required', 'min:2', 'max:50', Rule::unique('teams', 'name')->where('tournament_id', $this->tournament->id)->ignore($this->team->id)],
        ]);

        $this->team->update(['name' => $this->newName]);
        $this->toastSuccess(__('Team :name updated !', ['name' => $this->team->name]));
    }
}
