<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class RegisterForm extends Form
{
    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public $profilePicture;

    /** @return array<string, ValidationRule|array<int, mixed>|string> */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:40', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed:passwordConfirmation', Password::defaults()],
            'profilePicture' => ['file', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:3072'],
        ];
    }
}
