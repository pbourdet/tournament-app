<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Phases;

use App\Models\Tournament;
use App\Rules\Phases\GroupsCount;
use Tests\Unit\Rules\RuleTestCase;

class GroupsCountTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 17]);

        $rule = new GroupsCount($tournament);

        $this->assertValidationPasses($rule, 4);
    }

    public function testRuleFails(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 17]);

        $rule = new GroupsCount($tournament);

        $this->assertValidationFails($rule, [4]);
        $this->assertValidationFails($rule, 1);
        $this->assertValidationFails($rule, 4.0);
        $this->assertValidationFails($rule, 8);
    }
}
