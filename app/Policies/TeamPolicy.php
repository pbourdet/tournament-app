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

        return $tournament->players()->withoutTeamsInTournament($tournament)->where('id', $playerId)->exists()
            && $team->members->count() < $tournament->team_size;
    }

    public function removeMember(User $user, Team $team, User $player): bool
    {
        return $team->members->contains($player);
    }

    public function updateName(User $user, Team $team): bool
    {
        return $user->can('manage', $team->tournament) || $team->members->contains($user);
    }
}
