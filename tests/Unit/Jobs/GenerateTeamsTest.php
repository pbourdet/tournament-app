<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateTeams;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GenerateTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function testItGeneratesTheTeams(): void
    {
        $tournament = Tournament::factory()
            ->teamBased()
            ->full()
            ->create(['number_of_players' => 6]);

        $lock = Cache::lock($tournament->getLockKey(), 20);
        $lock->get();

        new GenerateTeams($tournament)->handle();

        $this->assertDatabaseCount('teams', 3);

        $teams = $tournament->refresh()->teams;
        $this->assertTrue($teams->every(fn (Team $team) => $team->isFull()));
        $this->assertSame(0, $tournament->players()->whereDoesntHave('teams')->count());

        $this->assertTrue($lock->get());
    }

    public function testItGenerateTeamsWithPlayersThatHaveTeamsInOtherTournament(): void
    {
        $tournament = Tournament::factory()->teamBased()->full()->create(['number_of_players' => 6]);
        Tournament::factory()->teamBased()->withPlayers($tournament->players)->create(['number_of_players' => 6]);

        new GenerateTeams($tournament)->handle();

        $teams = $tournament->refresh()->teams;
        $this->assertCount(3, $teams);
        $this->assertTrue($teams->every(fn (Team $team) => $team->isFull()));
    }

    public function testItDoesNotGenerateTeamsIsTournamentIsNotFull(): void
    {
        $tournament = Tournament::factory()->teamBased()->create();
        $lock = Cache::lock($tournament->getLockKey(), 20);
        $lock->get();

        new GenerateTeams($tournament)->handle();

        $this->assertTrue($tournament->teams->every(fn (Team $team) => 0 === $team->members->count()));
        $this->assertTrue($lock->get());
    }

    public function testItDoesNotGenerateTeamsIsTournamentIsNotTeamBased(): void
    {
        $tournament = Tournament::factory()->full()->create();
        $lock = Cache::lock($tournament->getLockKey(), 20);
        $lock->get();

        new GenerateTeams($tournament)->handle();

        $this->assertDatabaseCount('teams', 0);
        $this->assertTrue($lock->get());
    }
}
