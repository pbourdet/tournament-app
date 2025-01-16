<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Tournament\Phase;

use App\Models\Tournament;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Form;

class CreateGroupForm extends Form
{
    public int $numberOfGroups = 2;

    public int $contestantsQualifying = 2;

    /** @return array<string, array<string|ValidationRule>> */
    public function rules(): array
    {
        return [
            'numberOfGroups' => ['required', 'integer', 'min:2'],
            'playersQualifying' => ['required', 'integer', 'min:1'],
        ];
    }
}
