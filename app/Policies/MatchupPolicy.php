<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Matchup;
use App\Models\User;

class MatchupPolicy
{
    public function addResult(User $user, Matchup $match): bool
    {
        $tournament = $match->round->phase->tournament;

        return $user->can('manage', $tournament) && $tournament->isInProgress();
    }
}
