<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    private const array ORGANIZER_ABILITIES = [
        'addMember',
        'removeMember',
    ];

    public function before(User $user, string $ability, Team $team): ?bool
    {
        $tournament = $team->tournament;

        if (in_array($ability, self::ORGANIZER_ABILITIES) && $user->cannot('manage', $tournament)) {
            return false;
        }

        return null;
    }

    public function addMember(User $user, Team $team, string $playerId): bool
    {
        $tournament = $team->tournament;

        return null !== $tournament->playersWithoutTeams()->firstWhere('id', $playerId)
                && $tournament->isNotStarted()
                && !$team->isFull();
    }

    public function removeMember(User $user, Team $team, User $player): bool
    {
        return $team->members->contains($player) && $team->tournament->isNotStarted();
    }

    public function updateName(User $user, Team $team): bool
    {
        return $user->can('manage', $team->tournament) || $team->members->contains($user);
    }
}
