<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Phases;

use App\Models\Tournament;
use App\Rules\Phases\ContestantsQualifyingPerGroup;
use Tests\Unit\Rules\RuleTestCase;

class ContestantsQualifyingPerGroupTest extends RuleTestCase
{
    public function testRulePasses(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 17]);

        $rule = new ContestantsQualifyingPerGroup($tournament, 4);

        $this->assertValidationPasses($rule, 2);
    }

    public function testRuleFails(): void
    {
        $tournament = Tournament::factory()->teamBased()->create(['number_of_players' => 17]);

        $rule = new ContestantsQualifyingPerGroup($tournament, 4);

        $this->assertValidationFails($rule, [4]);
        $this->assertValidationFails($rule, 0);
        $this->assertValidationFails($rule, 4.0);
        $this->assertValidationFails($rule, 8);
    }
}
