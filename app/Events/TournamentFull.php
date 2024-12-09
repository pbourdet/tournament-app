<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

readonly class TournamentFull
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Tournament $tournament,
    ) {
    }
}
