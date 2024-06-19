<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class RuleTestCase extends TestCase
{
    use RefreshDatabase;

    protected string $attribute = 'attribute';

    abstract public function testRulePasses(): void;

    abstract public function testRuleFails(): void;

    protected function assertValidationPasses(ValidationRule $rule, mixed $value): void
    {
        $this->assertTrue($this->validate($rule, $value));
    }

    protected function assertValidationFails(ValidationRule $rule, mixed $value): void
    {
        $this->assertFalse($this->validate($rule, $value));
    }

    private function validate(ValidationRule $rule, mixed $value): bool
    {
        $isValid = true;
        $rule->validate($this->attribute, $value, function (string $message) use (&$isValid) {
            $isValid = false;
        });

        return $isValid;
    }
}
