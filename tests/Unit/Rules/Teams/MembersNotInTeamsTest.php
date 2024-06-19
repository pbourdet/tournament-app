<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Teams;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use App\Rules\Teams\MembersNotInTeams;
use Tests\Unit\Rules\RuleTestCase;

class MembersNotInTeamsTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $users = User::factory()->count(2)->create();
        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create();

        $rule = new MembersNotInTeams($tournament);

        $this->assertValidationPasses($rule, [$users[0]->id, $users[1]->id]);
    }

    public function testRuleFails(): void
    {
        $users = User::factory()->count(2)->create();
        $tournament = Tournament::factory()->teamBased()->create();
        Team::factory()->withMembers($users)->create(['tournament_id' => $tournament->id]);

        $rule = new MembersNotInTeams($tournament);

        $this->assertValidationFails($rule, [$users[0]->id, $users[1]->id]);
    }
}
