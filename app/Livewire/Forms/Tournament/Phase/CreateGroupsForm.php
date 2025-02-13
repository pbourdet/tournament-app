<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Tournament\Phase;

use App\Models\Tournament;
use App\Rules\Phases\ContestantsQualifyingPerGroup;
use App\Rules\Phases\GroupsCount;
use App\Rules\Phases\TotalContestantsQualifying;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Form;

class CreateGroupsForm extends Form
{
    private Tournament $tournament;

    public int $numberOfGroups = 2;

    public int $contestantsQualifying = 1;

    public function setTournament(Tournament $tournament): void
    {
        $this->tournament = $tournament;

        if (null !== $tournament->groupPhase) {
            $this->numberOfGroups = $tournament->groupPhase->number_of_groups;
            $this->contestantsQualifying = $tournament->groupPhase->qualifying_per_group;
        }
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
