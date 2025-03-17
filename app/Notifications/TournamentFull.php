<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ToastType;
use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TournamentFull extends Notification
{
    use Queueable;

    public function __construct(
        readonly private Tournament $tournament,
    ) {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'toastType' => ToastType::INFO->value,
            'message' => __('Your tournament :tournament is full ! You can start it once the tournaments phases are set up.', [
                'tournament' => $this->tournament->name,
            ]),
        ]);
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'translationKey' => __('Your tournament :tournament is full ! You can start it once the tournaments phases are set up.', locale: 'en'),
            'translationParams' => ['tournament' => $this->tournament->name],
        ];
    }
}
