<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TeamsGenerated;
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
        public readonly string $locale,
    ) {
    }

    public function handle(): void
    {
        app()->setLocale($this->locale);

        try {
            if (!$this->tournament->canGenerateTeams()) {
                return;
            }

            $teamSize = (int) $this->tournament->team_size;
            $missingTeamsCount = $this->tournament->missingTeamsCount();

            for ($i = 0; $i < $missingTeamsCount; ++$i) {
                $playersChunk = $this->tournament->players()
                     ->whereDoesntHave('teams')
                     ->take($teamSize)
                     ->get();

                if ($playersChunk->isNotEmpty()) {
                    $team = $this->tournament->teams()->create([
                        'name' => sprintf('%s %s', __('Team'), $playersChunk->first()->name),
                    ]);

                    $team->members()->attach($playersChunk);
                }
            }

            event(new TeamsGenerated($this->tournament));
        } finally {
            Cache::lock($this->tournament->getTeamsLockKey())->forceRelease();
        }
    }
}
