<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TournamentUpdated;
use App\Models\Tournament;
use App\Notifications\TeamsGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateTeams implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public readonly Tournament $tournament;

    public function __construct(Tournament $tournament)
    {
        $this->tournament = $tournament;
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [
            new WithoutOverlapping($this->tournament->getLockKey())->withPrefix('')->shared()->dontRelease(),
        ];
    }

    public function handle(): void
    {
        try {
            if (!$this->tournament->canGenerateTeams()) {
                return;
            }

            $playersWithoutTeam = $this->tournament->playersWithoutTeams()->shuffle();

            foreach ($this->tournament->teams as $team) {
                $currentCount = $team->members->count();
                $team->addMembers($playersWithoutTeam->splice(0, $this->tournament->team_size - $currentCount)->all());
            }

            event(new TournamentUpdated($this->tournament));
            $this->tournament->organizer->notify(new TeamsGenerated($this->tournament));
        } finally {
            Cache::lock($this->tournament->getLockKey(), 20)->forceRelease();
        }
    }
}
