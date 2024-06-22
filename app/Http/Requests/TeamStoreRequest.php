<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Tournament;
use App\Rules\Teams\MembersCount;
use App\Rules\Teams\MembersInTournament;
use App\Rules\Teams\MembersNotInTeams;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeamStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Tournament $tournament */
        $tournament = $this->route('tournament');

        return $tournament->team_based;
    }

    /** @return array<string, array<string|ValidationRule>> */
    public function rules(): array
    {
        /** @var Tournament $tournament */
        $tournament = $this->route('tournament');

        return [
            'name' => ['nullable', 'string', 'min:2', 'max:50'],
            'members' => ['required', 'array', new MembersCount($tournament), new MembersNotInTeams($tournament), new MembersInTournament($tournament)],
            'members.*' => ['required', 'uuid', 'exists:users,id'],
        ];
    }
}
