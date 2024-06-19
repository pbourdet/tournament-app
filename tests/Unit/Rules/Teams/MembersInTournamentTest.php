<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Teams;

use App\Models\Tournament;
use App\Models\User;
use App\Rules\Teams\MembersInTournament;
use Tests\Unit\Rules\RuleTestCase;

class MembersInTournamentTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $users = User::factory()->count(2)->create();
        $tournament = Tournament::factory()->withPlayers($users)->create();

        $rule = new MembersInTournament($tournament);

        $this->assertValidationPasses($rule, [$users[0]->id, $users[1]->id]);
    }

    public function testRuleFails(): void
    {
        $users = User::factory()->count(2)->create();
        $tournament = Tournament::factory()->create();

        $rule = new MembersInTournament($tournament);

        $this->assertValidationFails($rule, [$users[0]->id, $users[1]->id]);
    }
}
