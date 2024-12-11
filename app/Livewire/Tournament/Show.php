<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Jobs\GenerateTeams;
use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;

class Show extends Component
{
    public Tournament $tournament;

    #[Locked]
    public bool $generationInProgress = false;

    public function mount(Tournament $tournament): void
    {
        $this->authorize('view', $tournament);

        $this->tournament = $tournament;
    }

    public function boot(): void
    {
        $this->generationInProgress = $this->generationInProgress();
    }

    public function render(): View
    {
        $this->tournament->load('teams.members');

        return view('livewire.tournament.show');
    }

    public function generate(): void
    {
        $this->checkAuthorization();

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        GenerateTeams::dispatch($this->tournament);
        $this->generationInProgress = true;
        $this->toast(ToastType::INFO, __('Teams generation in progress'));
    }

    private function generationInProgress(): bool
    {
        $lock = Cache::lock($this->tournament->getTeamsLockKey(), 20);

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }

    private function checkAuthorization(): void
    {
        $this->authorize('manage', $this->tournament);

        if ($this->generationInProgress) {
            abort(409);
        }
    }
}
