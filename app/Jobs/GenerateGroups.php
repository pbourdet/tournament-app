<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TournamentUpdated;
use App\Models\GroupPhase;
use App\Notifications\GroupsGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateGroups implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public readonly GroupPhase $groupPhase;

    public function __construct(GroupPhase $groupPhase)
    {
        $this->groupPhase = $groupPhase;
    }

    public function handle(): void
    {
        $tournament = $this->groupPhase->tournament;

        if (!$this->groupPhase->canGenerateGroups()) {
            return;
        }

        $contestantsWithoutGroup = $this->groupPhase->contestantsWithoutGroup()->shuffle();

        foreach ($this->groupPhase->groups as $group) {
            $currentCount = $group->contestants->count();
            $group->addContestants($contestantsWithoutGroup->splice(0, $group->size - $currentCount));
        }

        event(new TournamentUpdated($tournament));
        $tournament->organizer->notify(new GroupsGenerated($tournament));
    }
}
