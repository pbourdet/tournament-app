<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Jobs\GenerateTeams;
use App\Livewire\Component;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;

class Teams extends Component
{
    public Tournament $tournament;

    #[Locked]
    public bool $generationInProgress = false;

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function boot(): void
    {
        $this->generationInProgress = $this->generationInProgress();
    }

    public function rendering(): void
    {
        $this->tournament->load(['teams.members']);
    }

    /** @return array<string, string> */
    public function getListeners(): array
    {
        return [
            sprintf('echo-private:App.Models.User.%s,TeamsGenerated', Auth::user()?->id) => 'generationDone',
        ];
    }

    public function generate(): void
    {
        $this->authorize('manage', $this->tournament);
        $this->abortIfGenerationInProgress();

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        GenerateTeams::dispatch($this->tournament);
        $this->generationInProgress = true;
        $this->toast(ToastType::INFO, __('Teams generation in progress'));
    }

    public function generationDone(): void
    {
        $this->generationInProgress = false;
        $this->toast(ToastType::SUCCESS, __('Teams generation done for tournament :tournament !', ['tournament' => $this->tournament->name]));
    }

    public function delete(Team $team): void
    {
        $this->authorize('manage', $this->tournament);
        $this->abortIfGenerationInProgress();

        $team->delete();
        $this->toast(ToastType::SUCCESS, __('Team :name deleted !', ['name' => $team->name]));
    }

    private function generationInProgress(): bool
    {
        $lock = Cache::lock($this->tournament->getTeamsLockKey(), 60);

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }

    private function abortIfGenerationInProgress(): void
    {
        if ($this->generationInProgress) {
            abort(409);
        }
    }
}
