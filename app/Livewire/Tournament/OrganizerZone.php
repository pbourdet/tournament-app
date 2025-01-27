<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;

class OrganizerZone extends Component
{
    public Tournament $tournament;

    public ?string $page;

    public string $title;

    /** @var array<string, string> */
    private const array SUPPORTED_PAGES = [
        'general' => 'General',
        'players' => 'Players',
        'teams' => 'Teams',
    ];

    public function mount(Tournament $tournament, ?string $page = null): void
    {
        if (Gate::denies('view', $tournament) || Gate::denies('manage', $tournament)) {
            session()->flash('toast', ['text' => __('You cannot see this page'), 'variant' => ToastType::DANGER->value]);

            [$redirectRoute, $params] = Gate::denies('view', $tournament)
                ? ['dashboard', []]
                : ['tournaments.show', ['tournament' => $tournament]];

            $this->redirectRoute($redirectRoute, $params, navigate: true);

            return;
        }

        $this->tournament = $tournament;

        $page = $this->sanitizePage($page);
        $this->page = $page;
        $this->title = self::SUPPORTED_PAGES[$page];
    }

    private function sanitizePage(?string $page): string
    {
        if (null === $page) return 'general';
        if (!in_array($page, self::SUPPORTED_PAGES, true)) return 'general';

        return $page;
    }
}
