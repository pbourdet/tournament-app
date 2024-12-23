<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Profile;

use Livewire\Form;

class PasswordForm extends Form
{
    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', 'confirmed:passwordConfirmation', 'min:8'],
        ];
    }
}
