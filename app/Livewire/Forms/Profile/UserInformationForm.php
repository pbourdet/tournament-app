<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Profile;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Form;

class UserInformationForm extends Form
{
    public User $user;

    public string $username = '';

    public string $email = '';

    public function hydrate(User $user): void
    {
        $this->user = $user;
        $this->username = $user->username;
        $this->email = $user->email;
    }

    /** @return array<string, array<int, Unique|string>> */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:40', Rule::unique(User::class)->ignore($this->user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
        ];
    }
}
