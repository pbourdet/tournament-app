<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Matchup;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

readonly class ResultAdded
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Matchup $match,
    ) {
    }
}
