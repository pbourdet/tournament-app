<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Contestant;
use App\Models\Group;
use App\Models\GroupPhase;
use App\Models\Tournament;
use App\Services\Generators\GroupsMatchesGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupsMatchesGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testGenerate(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 16]);
        $phase = GroupPhase::factory()->withRounds()->withFullGroups()->forTournament($tournament)->create([
            'number_of_groups' => 4,
        ]);
        $phase->refresh();

        new GroupsMatchesGenerator()->generate($phase);

        $this->assertDatabaseCount('matches', 24);
        $this->assertTrue($phase->groups->every(function (Group $group) {
            return $group->getContestants()->every(fn (Contestant $contestant) => 3 === $contestant->matches->count());
        }));
    }
}
