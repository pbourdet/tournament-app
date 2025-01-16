<?php

declare(strict_types=1);

namespace App\Rules\Phases;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class ContestantsQualifyingPerGroup implements ValidationRule
{
    public function __construct(
        public Tournament $tournament,
        public int $numberOfGroups,
    ) {
    }

    /** @param  \Closure(string, ?string=): PotentiallyTranslatedString  $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $groupSize = intdiv($this->tournament->contestantsCount(), $this->numberOfGroups);

        if ($value >= 1 && $value <= $groupSize) {
            return;
        }

        $fail($attribute, __('The number of contestants qualifying per group must be between 1 and the smallest number of contestants in a group.'));
    }
}
