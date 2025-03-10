<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

readonly class PlayerLeft
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Tournament $tournament,
        public User $player,
    ) {
    }
}
