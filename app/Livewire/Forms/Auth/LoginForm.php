<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Auth;

use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $rememberMe = false;

    /** @return array{email: string, password: string} */
    public function getCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
