<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Events\ResultAdded;
use App\Events\TournamentUpdated;
use App\Livewire\Component;
use App\Models\Contestant;
use App\Models\Matchup;
use Illuminate\Support\Facades\Gate;

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

        $this->match = $match->load('round.phase.tournament');
        $this->contestants = $contestantsArray;
    }

    public function addResult(): void
    {
        $tournament = $this->match->round->phase->tournament;

        if (Gate::denies('manage', $tournament) || !$tournament->isStarted()) {
            abort(403);
        }

        $this->match->results()->delete();

        foreach ($this->contestants as $id => $contestant) {
            $this->match->results()->create([
                'contestant_id' => $id,
                'contestant_type' => $this->match->getContestantType(),
                'outcome' => $contestant['outcome'],
                'score' => $contestant['score'],
            ]);
        }

        ResultAdded::dispatch($this->match);
        $this->toastSuccess(__('Result added !'));
        $this->modals()->close();
        event(new TournamentUpdated($tournament));
    }
}
