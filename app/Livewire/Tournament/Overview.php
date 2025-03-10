<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;

class Overview extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;
}
