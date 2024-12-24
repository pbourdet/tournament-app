<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class PasswordForm extends Form
{
    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    /** @return array<string, ValidationRule|array<int, mixed>|string> */
    public function rules(): array
    {
        return [
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', 'confirmed:passwordConfirmation', Password::defaults()],
        ];
    }
}
