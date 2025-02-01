<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Livewire\Component;
use App\Livewire\Tournament\WithTournamentLock;
use App\Models\Tournament;

class Players extends Component
{
    use WithTournamentLock;

    public Tournament $tournament;
}
