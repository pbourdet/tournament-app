<?php

declare(strict_types=1);

namespace App\Rules\Teams;

use App\Models\Tournament;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class MembersNotInTeams implements ValidationRule
{
    public function __construct(
        public Tournament $tournament
    ) {
    }

    /** @param \Closure(string, string): PotentiallyTranslatedString $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $count = $this->tournament->teams()->whereHas('members', function (Builder $query) use ($value) {
            $query->whereIn('users.id', $value);
        })->count();

        if (0 !== $count) {
            $fail($attribute, __('One or more selected members are already part of a team in this tournament.'));
        }
    }
}
