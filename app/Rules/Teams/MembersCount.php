<?php

declare(strict_types=1);

namespace App\Rules\Teams;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class MembersCount implements ValidationRule
{
    public function __construct(
        public Tournament $tournament
    ) {
    }

    /** @param \Closure(string, string): PotentiallyTranslatedString $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (is_array($value) && $this->tournament->team_size === count($value)) {
            return;
        }

        $fail($attribute, __('The number of team members must match the team size setting of the tournament.'));
    }
}
