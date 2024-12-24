<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Livewire\Component;

class Logout extends Component
{
    public function logout(): void
    {
        auth()->guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login', navigate: true);
    }
}
