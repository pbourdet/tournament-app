<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Phase;

use App\Livewire\Component;
use App\Models\Tournament;

class Qualification extends Component
{
    public Tournament $tournament;

    /** @var array<string, string> */
    public array $selectableContestants = [];
}
