<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Tournament\Phase;

use Livewire\Form;

class CreateEliminationForm extends Form
{
    public int $numberOfContestants = 2;

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'numberOfContestants' => ['required', 'integer', 'in:2,4,8,16,32,64,128'],
        ];
    }
}
