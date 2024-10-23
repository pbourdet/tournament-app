<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ToastType;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PlayerJoined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        readonly private Tournament $tournament,
        readonly private User $newPlayer,
    ) {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'toastType' => ToastType::INFO->value,
            'message' => __('Player :name has joined tournament :tournament', [
                'name' => $this->newPlayer->name,
                'tournament' => $this->tournament->name,
            ]),
        ]);
    }
}
