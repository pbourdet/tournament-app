<?php

declare(strict_types=1);

namespace App\Rules\Phases;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class GroupsCount implements ValidationRule
{
    public function __construct(
        public Tournament $tournament,
    ) {
    }

    /** @param  \Closure(string, ?string=): PotentiallyTranslatedString  $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (is_int($value) && $value >= 2 && $value <= floor($this->tournament->contestantsCount() / 2)) {
            return;
        }

        $fail($attribute, __('The number of groups must be between 2 and half the number of contestants.'));
    }
}
