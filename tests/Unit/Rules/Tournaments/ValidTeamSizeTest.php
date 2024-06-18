<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Tournaments;

use App\Rules\Tournaments\ValidTeamSize;
use Tests\Unit\Rules\RuleTestCase;

class ValidTeamSizeTest extends RuleTestCase
{
    public function testValidTeamSizePasses(): void
    {
        $rule = new ValidTeamSize(10);

        $this->assertValidationPasses($rule, 5);
        $this->assertValidationPasses($rule, 2);
    }

    public function testValidTeamSizeFails(): void
    {
        $rule = new ValidTeamSize(10);

        $this->assertValidationFails($rule, 3);
        $this->assertValidationFails($rule, 10);
        $this->assertValidationFails($rule, 1);
        $this->assertValidationFails($rule, 0);
    }
}
