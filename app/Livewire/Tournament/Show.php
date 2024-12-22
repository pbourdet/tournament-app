<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;

class Show extends Component
{
    /** @var array<int, string> */
    private const array SUPPORTED_PAGES = [
        'overview',
        'teams',
        'phase-elimination',
    ];

    public Tournament $tournament;

    public ?string $page;

    public function mount(Tournament $tournament, ?string $page = null): void
    {
        $this->authorize('view', $tournament);
        $this->tournament = $tournament;

        $this->page = 'tournament.'.str_replace('-', '.', $this->sanitizePage($page));
    }

    public function render(): View
    {
        $this->tournament->load('teams.members');

        return view('livewire.tournament.show');
    }

    private function sanitizePage(?string $page): string
    {
        if ('teams' === $page && !$this->tournament->team_based) {
            return 'overview';
        }

        return in_array($page, self::SUPPORTED_PAGES, true) ? $page : 'overview';
    }
}
