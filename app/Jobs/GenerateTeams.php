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

    public function __construct(
        public readonly Tournament $tournament,
    ) {
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

            $teamSize = (int) $this->tournament->team_size;
            $missingTeamsCount = $this->tournament->missingTeamsCount();

            for ($i = 0; $i < $missingTeamsCount; ++$i) {
                $playersChunk = $this->tournament->players()
                     ->withoutTeams()
                     ->take($teamSize)
                     ->get();

                if ($playersChunk->isNotEmpty()) {
                    $team = $this->tournament->teams()->create([
                        'name' => sprintf('%s %s', __('Team'), $playersChunk->first()->username),
                    ]);

                    $team->members()->attach($playersChunk);
                }
            }

            event(new TournamentUpdated($this->tournament));
            $this->tournament->organizer->notify(new TeamsGenerated($this->tournament));
        } finally {
            Cache::lock($this->tournament->getLockKey(), 20)->forceRelease();
        }
    }
}
