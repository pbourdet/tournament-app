<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Teams;

use App\Models\Tournament;
use App\Rules\Teams\MembersCount;
use Tests\Unit\Rules\RuleTestCase;

class MembersCountTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $tournament = Tournament::factory()->teamBased()->create();

        $rule = new MembersCount($tournament);

        $this->assertValidationPasses($rule, [1, 2]);
    }

    public function testRuleFails(): void
    {
        $tournament = Tournament::factory()->teamBased()->create();

        $rule = new MembersCount($tournament);

        $this->assertValidationFails($rule, [1]);
        $this->assertValidationFails($rule, [1, 2, 3]);
        $this->assertValidationFails($rule, 1);
    }
}
