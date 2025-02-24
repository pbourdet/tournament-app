<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ToastType;
use App\Models\User;
use Flux\Concerns\InteractsWithComponents;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    use InteractsWithComponents;

    #[Locked]
    public bool $organizerMode = false;

    #[Computed]
    public function user(): User
    {
        return User::findOrFail(auth()->id());
    }

    protected function toastSuccess(string $message): void
    {
        $this->toast($message, variant: ToastType::SUCCESS->value);
    }

    protected function toastError(string $message): void
    {
        $this->toast($message, variant: ToastType::DANGER->value);
    }
}
