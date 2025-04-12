<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Livewire\Component;
use App\Models\Contestant;
use App\Models\Group;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

/**
 * @property Collection<int, Contestant> $sortedContestants
 */
class GroupCard extends Component
{
    public Tournament $tournament;

    public Group $group;

    /** @var array<string, string> */
    public $selectableContestants = [];

    /** @return Collection<int, Contestant> */
    #[Computed]
    public function sortedContestants(): Collection
    {
        return $this->group->getSortedContestants();
    }

    /** @return array<string, array{win: int, tie: int, loss: int}> */
    #[Computed]
    public function matchesByContestants(): array
    {
        $matchesByContestant = [];

        foreach ($this->sortedContestants as $contestant) {
            $matches = $contestant->getMatchesForGroup($this->group);

            $matchesByContestant[$contestant->id] = [
                'win' => $matches->filter(fn ($match) => $contestant->won($match))->count(),
                'tie' => $matches->filter(fn ($match) => $contestant->tied($match))->count(),
                'loss' => $matches->filter(fn ($match) => $contestant->lost($match))->count(),
            ];
        }

        return $matchesByContestant;
    }
}
