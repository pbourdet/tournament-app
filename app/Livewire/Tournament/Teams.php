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

class Teams extends Component
{
    public Tournament $tournament;

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
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

        if (!$this->tournament->canGenerateTeams()) {
            abort(403);
        }

        $this->checkLock();

        GenerateTeams::dispatch($this->tournament, app()->getLocale());
        $this->toast(ToastType::INFO, __('Teams generation in progress'));
    }

    public function generationDone(): void
    {
        $this->toast(ToastType::SUCCESS, __('Teams generation done for tournament :tournament !', ['tournament' => $this->tournament->name]));
    }

    public function delete(Team $team): void
    {
        $this->authorize('manage', $this->tournament);

        $team->delete();
        $this->toast(ToastType::SUCCESS, __('Team :name deleted !', ['name' => $team->name]));
    }

    private function checkLock(): void
    {
        $lock = Cache::lock($this->tournament->getTeamsLockKey(), 60);

        if (!$lock->get()) {
            abort(409);
        }
    }
}
