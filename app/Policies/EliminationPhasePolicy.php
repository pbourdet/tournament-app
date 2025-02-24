<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class EliminationPhasePolicy
{
    public function create(User $user, Tournament $tournament): bool
    {
        return $user->can('manage', $tournament)
            && $tournament->eliminationPhase()->doesntExist()
            && $tournament->isNotStarted();
    }
}
