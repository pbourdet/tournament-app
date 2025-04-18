<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Events\ResultAdded;
use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Models\Contestant;
use App\Models\Matchup;

/**
 * @phpstan-type contestantsArray = array<string, array{name: string, outcome: ResultOutcome, score: int}>
 */
class MatchCard extends Component
{
    public Matchup $match;

    /** @var contestantsArray */
    public array $contestants = [];

    public function mount(Matchup $match): void
    {
        /** @var contestantsArray $contestantsArray */
        $contestantsArray = $this->match->getContestants()->mapWithKeys(
            function (Contestant $contestant): array {
                $result = $this->match->getResultFor($contestant);

                return [
                    $contestant->id => [
                        'name' => $contestant->getName(),
                        'outcome' => $result?->outcome,
                        'score' => $result?->score,
                    ],
                ];
            })->toArray();

        $this->match = $match;
        $this->contestants = $contestantsArray;
    }

    public function addResult(): void
    {
        $this->authorize('addResult', $this->match);
        $tournament = $this->match->round->phase->tournament;

        $this->match->results()->delete();

        foreach ($this->contestants as $id => $contestant) {
            $this->match->results()->create([
                'contestant_id' => $id,
                'contestant_type' => $this->match->getContestantType(),
                'outcome' => $contestant['outcome'],
                'score' => $contestant['score'],
            ]);
        }

        event(new ResultAdded($this->match));
        event(new TournamentUpdated($tournament, shouldUpdateStatus: false));
        $this->toastSuccess(__('Result added !'));
        $this->modals()->close();
    }
}
