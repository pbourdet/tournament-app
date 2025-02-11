<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Group;
use App\Models\Tournament;

class GroupCard extends Component
{
    public Tournament $tournament;

    public Group $group;

    /** @var array<string, string> */
    public $selectableContestants = [];
}
