<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate();

        Password::sendResetLink(['email' => $this->email]);

        $this->toast(__('A password reset link has been emailed to :email.', ['email' => $this->email]));
        $this->reset();
    }
}
