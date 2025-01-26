<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;

class OrganizerZone extends Component
{
    public Tournament $tournament;

    public ?string $page;

    public function mount(Tournament $tournament, ?string $page = null): void
    {
        $this->authorize('manage', $tournament);
        $this->tournament = $tournament;
        $this->page = $page ?? 'general';
    }
}
