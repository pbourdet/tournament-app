<?php

declare(strict_types=1);

namespace App\Rules\Phases;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class TotalContestantsQualifying implements ValidationRule
{
    public function __construct(
        public Tournament $tournament,
        public int $numberOfGroups,
    ) {
    }

    /** @param \Closure(string, ?string=): PotentiallyTranslatedString $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (null === $this->tournament->eliminationPhase) {
            return;
        }

        if (is_int($value) && $value * $this->numberOfGroups <= $this->tournament->eliminationPhase->configuration->numberOfContestants) {
            return;
        }

        $fail($attribute, __('Total of contestants qualifying must be less or equal to number of contestants of the elimination phase.'));
    }
}
