<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TournamentUpdated;
use App\Models\Phase;
use App\Models\Tournament;
use App\Notifications\TournamentStarted;
use App\Services\Generators\Generator;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class StartTournament implements ShouldQueue
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

    /**
     * @param array<int, Generator<Phase>> $roundsGenerator
     * @param array<int, Generator<Phase>> $matchesGenerators
     */
    public function handle(
        #[Tag('rounds_generators')] iterable $roundsGenerator,
        #[Tag('matches_generators')] iterable $matchesGenerators,
    ): void {
        try {
            $phases = $this->tournament->getPhases();

            foreach ($phases as $phase) {
                foreach ($roundsGenerator as $generator) {
                    if ($generator->supports($phase)) {
                        $generator->generate($phase);
                    }
                }

                foreach ($matchesGenerators as $generator) {
                    if ($generator->supports($phase)) {
                        $generator->generate($phase);
                    }
                }
            }

            $this->tournament->start();
            Notification::send($this->tournament->players, new TournamentStarted($this->tournament));
            event(new TournamentUpdated($this->tournament));
        } finally {
            Cache::lock($this->tournament->getLockKey(), 20)->forceRelease();
        }
    }
}
