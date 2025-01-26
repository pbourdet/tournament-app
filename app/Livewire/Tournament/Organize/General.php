<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Livewire\Component;
use App\Models\Tournament;

class General extends Component
{
    public Tournament $tournament;

    public string $name = '';

    public function mount(Tournament $tournament): void
    {
        $this->name = $tournament->name;
    }

    public function update(): void
    {
    }
}
