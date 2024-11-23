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

        Team::factory()->withMembers($tournament->players()->take(2)->get())->create([
            'tournament_id' => $tournament->id,
            'name' => 'created team',
        ]);

        $lock = Cache::lock($tournament->getTeamsLockKey(), 20);
        $lock->get();

        (new GenerateTeams($tournament))->handle();

        $this->assertDatabaseCount('teams', 3);

        $teams = $tournament->teams;
        foreach ($teams as $team) {
            $this->assertCount(2, $team->members);
        }
        $this->assertSame(0, $tournament->players()->whereDoesntHave('teams')->count());

        $this->assertTrue($lock->get());
    }

    public function testItDoesNotCreateTeamsIsTournamentIsNotFull(): void
    {
        $tournament = Tournament::factory()->teamBased()->create();
        $lock = Cache::lock($tournament->getTeamsLockKey(), 20);
        $lock->get();

        (new GenerateTeams($tournament))->handle();

        $this->assertDatabaseCount('teams', 0);
        $this->assertTrue($lock->get());
    }

    public function testItDoesNotCreateTeamsIsTournamentIsNotTeamBased(): void
    {
        $tournament = Tournament::factory()->full()->create();
        $lock = Cache::lock($tournament->getTeamsLockKey(), 20);
        $lock->get();

        (new GenerateTeams($tournament))->handle();

        $this->assertDatabaseCount('teams', 0);
        $this->assertTrue($lock->get());
    }
}
