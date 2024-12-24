<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VerifyEmail extends Component
{
    public function mount(): void
    {
        $user = User::findOrFail(auth()->id());

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(route('dashboard'), navigate: true);
        }
    }
}
