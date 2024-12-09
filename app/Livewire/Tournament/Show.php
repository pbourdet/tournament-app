<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Jobs\StartTournament;
use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;

class Show extends Component
{
    public Tournament $tournament;

    public bool $generationInProgress = false;

    public function mount(Tournament $tournament): void
    {
        Gate::authorize('view', $tournament);

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

    #[On('phase-created')]
    public function phaseCreated(): void
    {
    }

    /** @return array<string, string> */
    public function getListeners(): array
    {
        return [
            sprintf('echo-private:App.Models.User.%s,TournamentStarted', Auth::user()?->id) => 'started',
        ];
    }

    public function started(): void
    {
        $this->generationInProgress = false;
    }

    public function start(): void
    {
        Gate::authorize('manage', $this->tournament);

        if (!$this->tournament->isReadyToStart()) {
            abort(403);
        }

        StartTournament::dispatch($this->tournament);
        $this->generationInProgress = true;
        $this->toast(ToastType::INFO, __('The tournament will soon start ! Matches are being generated and players will be notified.'));
    }

    private function generationInProgress(): bool
    {
        $lock = Cache::lock($this->tournament->getLockKey(), 60);

        if ($lock->get()) {
            $lock->release();

            return false;
        }

        return true;
    }
}
