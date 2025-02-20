<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VerifyEmail extends Component
{
    public function mount(): void
    {
        if ($this->user()->hasVerifiedEmail()) {
            $this->redirectIntended(route('dashboard'), navigate: true);
        }
    }
}
