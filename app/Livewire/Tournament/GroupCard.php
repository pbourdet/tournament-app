<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Group;
use App\Models\Tournament;
use Livewire\Attributes\Locked;

class GroupCard extends Component
{
    public Tournament $tournament;

    public Group $group;

    #[Locked]
    public bool $organizerMode;

    /** @var array<string, string> */
    public $selectableContestants = [];
}
