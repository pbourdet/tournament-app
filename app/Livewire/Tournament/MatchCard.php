<?php

declare(strict_types=1);

namespace App\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Enums\ToastType;
use App\Events\ResultAdded;
use App\Livewire\Component;
use App\Models\Matchup;
use App\Models\Team;
use App\Models\User;
use Illuminate\View\View;

class MatchCard extends Component
{
    public Matchup $match;

    /** @var array<string, array{outcome: ResultOutcome, score: int}> */
    public array $contestants = [];

    public function mount(Matchup $match): void
    {
        /** @var array<string, array{outcome: ResultOutcome, score: int}> $contestantsArray */
        $contestantsArray = $this->match->getContestants()->mapWithKeys(
            function (User|Team $contestant): array {
                $result = $this->match->getResultFor($contestant);

                return [
                    $contestant->id => [
                        'name' => $contestant->name,
                        'outcome' => $result?->outcome,
                        'score' => $result?->score,
                    ],
                ];
            })->toArray();

        $this->contestants = $contestantsArray;
        $this->match = $match;
    }

    public function render(): View
    {
        return view('livewire.tournament.match-card');
    }

    public function addResult(): void
    {
        $this->match->results()->delete();

        foreach ($this->contestants as $key => $contestant) {
            $this->match->results()->create([
                'contestant_id' => $key,
                'contestant_type' => $this->match->getContestants()->first()?->getMorphClass(),
                'outcome' => $contestant['outcome'],
                'score' => $contestant['score'],
            ]);
        }

        ResultAdded::dispatch($this->match);
        $this->toast(__('Result added !'), variant: ToastType::SUCCESS->value);
        $this->modals()->close();
    }
}
