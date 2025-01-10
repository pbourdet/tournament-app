<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\RoundStage;
use App\Models\Matchup;
use App\Models\Phase;
use App\Models\Tournament;
use App\Services\Generators\EliminationMatchesGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminationMatchesGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testGenerate(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 16]);
        $phase = Phase::factory()->withRounds()->forTournament($tournament)->withConfiguration(['numberOfContestants' => 16])->create();

        new EliminationMatchesGenerator()->generate($phase);

        $this->assertDatabaseCount('matches', 15);
        $this->assertDatabaseCount('match_contestant', 16);

        $firstRound = $phase->rounds()->where('stage', RoundStage::R16)->firstOrFail();
        $firstRound->matches()->each(function ($match) {
            $this->assertCount(2, $match->contestants);
        });

        $otherRounds = $phase->rounds()->where('stage', '!=', RoundStage::R16)->get();
        $otherRounds->each(function ($round) {
            $round->matches()->each(function ($match) {
                $this->assertCount(0, $match->contestants);
            });
        });

        $contestantIds = $firstRound->matches()->get()->flatMap(fn (Matchup $match) => $match->contestants()->pluck('id'));
        $this->assertCount(16, $contestantIds);
    }
}
