<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PlayerLeft;
use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;

class PlayerLeftListener
{
    private Tournament $tournament;

    private User $player;

    public function handle(PlayerLeft $event): void
    {
        $this->tournament = $event->tournament;
        $this->player = $event->player;

        $this->removePlayerFromTeam();
        $this->removePlayerFromGroup();
    }

    private function removePlayerFromTeam(): void
    {
        $team = $this->tournament->teams->first(fn (Team $team) => $team->members->contains($this->player));

        if (null === $team) return;

        $team->members()->detach($this->player);
    }

    private function removePlayerFromGroup(): void
    {
        if ($this->tournament->team_based) return; // We cannot remove players from groups in team-based tournaments
        if (null === $this->tournament->groupPhase) return;

        $group = $this->tournament->groupPhase->groups->first(fn (Group $group) => $group->getContestants()->contains($this->player));

        if (null === $group) return;

        $group->contestants()->where('contestant_id', $this->player->id)->delete();
    }
}
