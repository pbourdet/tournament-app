<?php

declare(strict_types=1);

namespace App\Rules\Teams;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class MembersInTournament implements ValidationRule
{
    public function __construct(
        public Tournament $tournament
    ) {
    }

    /** @param \Closure(string, string): PotentiallyTranslatedString $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (is_array($value) && count($value) !== $this->tournament->players()->whereIn('users.id', $value)->count()) {
            $fail($attribute, __('One or more selected members are not part of this tournament.'));
        }
    }
}
