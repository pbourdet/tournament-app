<?php

declare(strict_types=1);

namespace App\Livewire;

class Notifications extends Component
{
    public function readAll(): void
    {
        $this->user->notifications->markAsRead();
    }

    public function deleteAll(): void
    {
        $this->user->notifications()->delete();
    }
}
