<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Phase;
use App\Models\Tournament;
use App\Services\Generators\EliminationRoundsGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminationRoundsGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testGenerate(): void
    {
        $tournament = Tournament::factory()->create(['number_of_players' => 16]);
        Phase::factory()->forTournament($tournament)->withConfiguration(['numberOfContestants' => 16])->create();

        new EliminationRoundsGenerator()->generate($tournament->eliminationPhase);

        $this->assertDatabaseCount('rounds', 4);
        $this->assertDatabaseHas('rounds', ['stage' => 'W16']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W8']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W4']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W2']);
    }
}
