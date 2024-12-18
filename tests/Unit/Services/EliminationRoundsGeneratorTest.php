<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Tournament;
use App\Services\EliminationRoundsGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EliminationRoundsGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testGenerate(): void
    {
        $tournament = Tournament::factory()->create(['number_of_players' => 16]);
        $phase = $tournament->eliminationPhase()->create(['number_of_contestants' => 16]);

        new EliminationRoundsGenerator()->generate($phase);

        $this->assertDatabaseCount('rounds', 4);
        $this->assertDatabaseHas('rounds', ['stage' => 'W16']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W8']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W4']);
        $this->assertDatabaseHas('rounds', ['stage' => 'W2']);
    }
}
