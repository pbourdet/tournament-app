<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GroupPhase;
use App\Models\Tournament;
use App\Models\User;

class GroupPhasePolicy
{
    public function before(User $user, string $ability, GroupPhase|string|null $groupPhase, Tournament $tournament): ?bool
    {
        if ($user->can('manage', $tournament) && true === $tournament->isNotStarted()) {
            return null;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function generateGroups(User $user, GroupPhase $groupPhase): bool
    {
        return $groupPhase->canGenerateGroups();
    }
}
