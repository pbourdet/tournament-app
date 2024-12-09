<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\TournamentStatus;
use App\Events\TournamentStarted;
use App\Models\Tournament;
use App\Services\EliminationMatchesGenerator;
use App\Services\EliminationRoundsGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
            (new WithoutOverlapping($this->tournament->getLockKey()))->withPrefix('')->shared()->dontRelease(),
        ];
    }

    public function handle(EliminationRoundsGenerator $roundsGenerator, EliminationMatchesGenerator $matchesGenerator): void
    {
        try {
            $roundsGenerator->generate($this->tournament->eliminationPhase()->firstOrFail());
            $matchesGenerator->generate($this->tournament->eliminationPhase()->firstOrFail());

            $this->tournament->update(['status' => TournamentStatus::IN_PROGRESS]);
            event(new TournamentStarted($this->tournament));
        } finally {
            Cache::lock($this->tournament->getLockKey(), 20)->forceRelease();
        }
    }
}
