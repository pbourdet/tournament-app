<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;

trait WithTournamentLock
{
    #[Locked]
    public bool $locked = false;

    public function boot(): void
    {
        $this->locked = $this->isLocked();
    }

    public function checkLock(): void
    {
        $this->authorize('manage', $this->tournament);

        if ($this->locked) {
            abort(409);
        }
    }

    private function isLocked(): bool
    {
        $lock = Cache::lock($this->tournament->getLockKey(), 20);

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }
}
