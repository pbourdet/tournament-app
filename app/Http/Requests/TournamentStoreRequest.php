<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TournamentStoreRequest extends FormRequest
{
    /** @return array<string, Rule|string[]|string> */
    public function rules(): array
    {
        return [
            'description' => ['nullable'],
            'number_of_players' => ['required', 'integer', 'max:256', 'min:2'],
            'name' => ['required', 'string', 'min:2', 'max:256'],
            'join_tournament' => ['required', 'bool'],
        ];
    }
}
