<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    public function manage(User $user, Tournament $tournament): bool
    {
        return $tournament->organizer->is($user);
    }

    public function join(User $user, Tournament $tournament): bool
    {
        return !$tournament->isFull()
            && !$tournament->players->contains($user)
            && ($tournament->invitation?->isNotExpired() ?? false);
    }

    public function create(User $user): bool
    {
        return $user->managedTournaments()->count() < 2;
    }

    public function view(User $user, Tournament $tournament): bool
    {
        return $tournament->players->contains($user) || $tournament->organizer()->is($user);
    }

    public function generateTeams(User $user, Tournament $tournament): bool
    {
        return $user->can('manage', $tournament) && $tournament->canGenerateTeams() && $tournament->isNotStarted();
    }

    public function removePlayer(User $user, Tournament $tournament, User $player): bool
    {
        return $user->can('manage', $tournament)
            && $tournament->players->contains($player)
            && $tournament->isNotStarted();
    }

    public function start(User $user, Tournament $tournament): bool
    {
        return $user->can('manage', $tournament) && $tournament->isReadyToStart();
    }

    public function deletePhase(User $user, Tournament $tournament): bool
    {
        return $user->can('manage', $tournament) && $tournament->isNotStarted();
    }
}
