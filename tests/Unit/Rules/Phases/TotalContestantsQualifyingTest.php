<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Phases;

use App\Models\Phase;
use App\Models\Tournament;
use App\Rules\Phases\TotalContestantsQualifying;
use Tests\Unit\Rules\RuleTestCase;

class TotalContestantsQualifyingTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 35]);
        Phase::factory()->forTournament($tournament)->withConfiguration(['numberOfContestants' => 16])->create();

        $rule = new TotalContestantsQualifying($tournament, 4);

        $this->assertValidationPasses($rule, 4);
        $this->assertValidationPasses($rule, 2);
    }

    public function testRuleFails(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 17]);
        Phase::factory()->forTournament($tournament)->withConfiguration(['numberOfContestants' => 16])->create();

        $rule = new TotalContestantsQualifying($tournament, 4);

        $this->assertValidationFails($rule, [4]);
        $this->assertValidationFails($rule, 17);
        $this->assertValidationFails($rule, 2.0);
        $this->assertValidationFails($rule, 8);
    }
}
