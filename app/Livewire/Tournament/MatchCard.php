<?php

namespace App\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Models\Matchup;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Component;

class MatchCard extends Component
{
    public Matchup $match;

    public function render(): View
    {
        $this->match->load('results.contestant')->loadContestants('.results');

        return view('livewire.tournament.match-card');
    }

    public function addResult(): void
    {
        $contestants = $this->match->getContestants();

        $this->match->results()->create([
            'contestant_id' => $contestants->first()->id,
            'contestant_type' => Team::class,
            'score' => 1,
            'outcome' => ResultOutcome::TIE->value,
        ]);
        $this->match->results()->create([
            'contestant_id' => $contestants->last()->id,
            'contestant_type' => Team::class,
            'score' => 1,
            'outcome' => ResultOutcome::TIE->value,
        ]);

        $this->match->refresh();
    }
}
