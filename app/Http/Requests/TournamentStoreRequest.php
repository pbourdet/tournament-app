<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidTeamSize;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TournamentStoreRequest extends FormRequest
{
    /** @return array<string, array<string|ValidationRule>> */
    public function rules(): array
    {
        return [
            'description' => ['nullable'],
            'number_of_players' => ['required', 'integer', 'max:256', 'min:2'],
            'name' => ['required', 'string', 'min:2', 'max:256'],
            'join_tournament' => ['required', 'boolean'],
            'team_based' => ['required', 'boolean'],
            'team_size' => ['exclude_if:team_based,false', 'required', 'integer', 'min:2', 'max:128', new ValidTeamSize($this->integer('number_of_players'))],
        ];
    }
}
