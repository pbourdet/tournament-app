<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    public function manage(User $user, Tournament $tournament): bool
    {
        return $tournament->organizer()->is($user);
    }
}
