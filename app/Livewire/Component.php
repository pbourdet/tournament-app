<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ToastType;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    public function toast(ToastType $type, string $message): void
    {
        $this->dispatch('toast-trigger', type: $type->value, message: $message);
    }
}
