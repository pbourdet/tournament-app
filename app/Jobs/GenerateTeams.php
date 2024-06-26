<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
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

    public function handle(): void
    {
        try {
            if (!$this->tournament->isFull() || !$this->tournament->team_based) {
                return;
            }

            $teamSize = (int) $this->tournament->team_size;

            do {
                $playersChunk = $this->tournament->players()
                    ->whereDoesntHave('teams')
                    ->take($teamSize)
                    ->get();

                if ($playersChunk->isNotEmpty()) {
                    $team = $this->tournament->teams()->create([
                        'name' => $this->tournament->getNextTeamName(),
                    ]);

                    $team->members()->attach($playersChunk);
                }
            } while ($playersChunk->count() === $teamSize);
        } finally {
            Cache::lock(sprintf('tournament:%s:generate-teams', $this->tournament->id))->forceRelease();
        }
    }
}
