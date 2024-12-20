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

    public function join(User $user, Tournament $tournament): bool
    {
        return !$tournament->isFull()
            && !$tournament->players->contains($user);
    }

    public function create(User $user): bool
    {
        return $user->managedTournaments()->count() < 2;
    }

    public function view(User $user, Tournament $tournament): bool
    {
        return $tournament->players->contains($user) || $tournament->organizer()->is($user);
    }
}
