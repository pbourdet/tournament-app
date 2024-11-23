<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Tournament;
use App\Rules\Teams\MembersCount;
use App\Rules\Teams\MembersInTournament;
use App\Rules\Teams\MembersNotInTeams;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateTeamForm extends Form
{
    public Tournament $tournament;

    #[Validate]
    public string $name = '';

    /** @var array<int, string> */
    #[Validate]
    public array $members = [];

    /** @return array<string, array<string|ValidationRule|Unique>> */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:2', 'max:50', Rule::unique('teams', 'name')->where('tournament_id', $this->tournament->id)],
            'members' => ['required', 'array', new MembersCount($this->tournament), new MembersNotInTeams($this->tournament), new MembersInTournament($this->tournament)],
            'members.*' => ['required', 'uuid', 'exists:users,id'],
        ];
    }
}
