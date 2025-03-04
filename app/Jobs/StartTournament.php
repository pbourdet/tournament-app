<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TournamentUpdated;
use App\Models\Tournament;
use App\Notifications\TournamentStarted;
use App\Services\Generators\PhaseMatchesGenerator;
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

    public function handle(PhaseMatchesGenerator $generator): void
    {
        try {
            $phase = $this->tournament->qualificationPhase() ?? $this->tournament->eliminationPhase;

            if (null === $phase) return;

            $generator->generate($phase);

            $this->tournament->start();
            Notification::send($this->tournament->players, new TournamentStarted($this->tournament));
            event(new TournamentUpdated($this->tournament));
        } finally {
            Cache::lock($this->tournament->getLockKey(), 20)->forceRelease();
        }
    }
}
