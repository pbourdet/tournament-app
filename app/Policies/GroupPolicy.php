<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contestant;
use App\Models\Group;
use App\Models\Tournament;
use App\Models\User;

class GroupPolicy
{
    public function before(User $user, string $ability, Group $group, Contestant $contestant, Tournament $tournament): ?bool
    {
        if (null !== $tournament->groupPhase && $tournament->groupPhase->groups->doesntContain($group)) {
            return false;
        }

        if ($user->cannot('manage', $tournament)) return false;

        return null;
    }

    public function addContestant(User $user, Group $group, Contestant $contestant, Tournament $tournament): bool
    {
        return $tournament->isNotStarted()
            && !$group->isFull()
            && $tournament->contestantsWithoutGroup()->contains($contestant)
            && !$group->getContestants()->contains($contestant);
    }

    public function removeContestant(User $user, Group $group, Contestant $contestant, Tournament $tournament): bool
    {
        return $tournament->isNotStarted()
            && $tournament->contestants()->contains($contestant)
            && $group->getContestants()->contains($contestant);
    }
}
