<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Events\TournamentUpdated;
use App\Models\Phase;

trait WithDeletePhaseAction
{
    public function deletePhase(string $phaseId): void
    {
        $this->authorize('deletePhase', $this->tournament);

        $this->tournament->getPhases()->firstOrFail(fn (Phase $phase) => $phase->id === $phaseId)->delete();
        event(new TournamentUpdated($this->tournament, broadcastToCurrentUser: false));
        $this->toastSuccess(__('Phase deleted !'));
    }
}
