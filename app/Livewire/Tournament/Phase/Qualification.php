<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Enums\PhaseType;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupForm;
use App\Models\Tournament;

class Qualification extends Component
{
    public Tournament $tournament;

    public PhaseType $type = PhaseType::GROUP;

    public CreateGroupForm $groupForm;

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function create(): void
    {
        //TODO
    }
}
