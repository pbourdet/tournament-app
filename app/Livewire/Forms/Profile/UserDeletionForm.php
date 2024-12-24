<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Profile;

use Livewire\Form;

class UserDeletionForm extends Form
{
    public string $password = '';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}
