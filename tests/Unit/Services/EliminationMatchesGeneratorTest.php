<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\EliminationRoundStage;
use App\Models\EliminationPhase;
use App\Models\Matchup;
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
        $phase = EliminationPhase::factory()->forTournament($tournament)->withRounds()->create(['number_of_contestants' => 16]);

        new EliminationMatchesGenerator()->generate($phase);

        $this->assertDatabaseCount('matches', 15);
        $this->assertDatabaseCount('match_contestant', 16);

        $firstRound = $phase->rounds()->where('stage', EliminationRoundStage::R16->value)->firstOrFail();
        $firstRound->matches()->each(function ($match) {
            $this->assertCount(2, $match->contestants);
        });

        $otherRounds = $phase->rounds()->where('stage', '!=', EliminationRoundStage::R16->value)->get();
        $otherRounds->each(function ($round) {
            $round->matches()->each(function ($match) {
                $this->assertCount(0, $match->contestants);
            });
        });

        $contestantIds = $firstRound->matches()->get()->flatMap(fn (Matchup $match) => $match->contestants()->pluck('id'));
        $this->assertCount(16, $contestantIds);
    }
}
