<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Tournament\Phase;

use App\Models\Tournament;
use App\Rules\Phases\ContestantsQualifyingPerGroup;
use App\Rules\Phases\GroupsCount;
use App\Rules\Phases\TotalContestantsQualifying;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Form;

class CreateGroupForm extends Form
{
    private Tournament $tournament;

    public int $numberOfGroups = 2;

    public int $contestantsQualifying = 2;

    public function setTournament(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    /** @return array<string, array<string|ValidationRule>> */
    public function rules(): array
    {
        return [
            'numberOfGroups' => ['required', 'integer', 'min:2', new GroupsCount($this->tournament)],
            'contestantsQualifying' => [
                'required',
                'integer',
                'min:1',
                new ContestantsQualifyingPerGroup($this->tournament, $this->numberOfGroups),
                new TotalContestantsQualifying($this->tournament, $this->numberOfGroups),
            ],
        ];
    }
}
