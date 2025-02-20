<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;

class Show extends Component
{
    /** @var array<string, string> */
    private const array SUPPORTED_PAGES = [
        'overview' => 'Overview',
        'teams' => 'Teams',
        'phase-elimination' => 'Elimination',
        'phase-qualification' => 'Qualification',
    ];

    public Tournament $tournament;
    public ?string $page;
    public string $title;

    public function mount(Tournament $tournament, ?string $page = null): void
    {
        $this->organizerMode = false;
        $this->authorize('view', $tournament);
        $this->tournament = $tournament;

        $page = $this->sanitizePage($page);

        $this->title = self::SUPPORTED_PAGES[$page];
        $this->page = 'tournament.'.str_replace('-', '.', $page);
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

        return in_array($page, array_keys(self::SUPPORTED_PAGES), true) ? $page : 'overview';
    }
}
