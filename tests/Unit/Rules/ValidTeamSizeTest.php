<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\ValidTeamSize;
use Illuminate\Translation\PotentiallyTranslatedString;
use Tests\TestCase;

class ValidTeamSizeTest extends TestCase
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

    private function assertValidationPasses(ValidTeamSize $rule, mixed $value): void
    {
        $failures = false;
        $rule->validate('team_size', $value, function (PotentiallyTranslatedString $message) use (&$failures) {
            $failures = true;
        });

        $this->assertFalse($failures);
    }

    private function assertValidationFails(ValidTeamSize $rule, mixed $value): void
    {
        $failures = false;
        $rule->validate('team_size', $value, function (string $attribute, string $message) use (&$failures) {
            $failures = true;
        });

        $this->assertTrue($failures);
    }
}
