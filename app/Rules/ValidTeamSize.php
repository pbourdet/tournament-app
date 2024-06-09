<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class ValidTeamSize implements ValidationRule
{
    public function __construct(
        public int $numberOfPlayers
    ) {
    }

    /** @param  \Closure(string, string): PotentiallyTranslatedString  $fail */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if ($value >= 2
            && $value < $this->numberOfPlayers
            && 0 === $this->numberOfPlayers % $value
        ) {
            return;
        }

        $fail($attribute, __('Team size must be greater than 1, less than the number of players and a multiple of the number of players.'));
    }
}
