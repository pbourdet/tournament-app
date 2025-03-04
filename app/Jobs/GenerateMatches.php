<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TournamentUpdated;
use App\Models\Phase;
use App\Services\Generators\PhaseMatchesGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMatches implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Phase $phase,
    ) {
    }

    public function handle(PhaseMatchesGenerator $generator): void
    {
        $generator->generate($this->phase);

        event(new TournamentUpdated($this->phase->tournament));
    }
}
