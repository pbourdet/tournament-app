<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class Invitation extends Component
{
    public Tournament $tournament;

    public function refresh(): void
    {
        Gate::authorize('manage', $this->tournament);

        $this->tournament->invitation?->delete();
        $this->tournament->invitation()->create([
            'code' => mb_strtoupper(Str::random(6)),
            'expires_at' => now()->addDays(7),
        ]);
        $this->tournament->refresh();
    }

    public function delete(): void
    {
        Gate::authorize('manage', $this->tournament);

        $this->tournament->invitation?->delete();
        $this->tournament->refresh();
    }
}
