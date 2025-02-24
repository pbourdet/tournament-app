<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;

class Invitation extends Component
{
    public Tournament $tournament;

    public function refresh(): void
    {
        $this->authorize('manage', $this->tournament);

        $this->tournament->invitation?->delete();
        $this->tournament->createInvitation();
        $this->tournament->refresh();
    }

    public function delete(): void
    {
        $this->authorize('manage', $this->tournament);

        $this->tournament->invitation?->delete();
        $this->tournament->refresh();
    }
}
