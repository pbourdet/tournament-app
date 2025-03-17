<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ToastType;
use App\Models\Tournament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GroupsGenerated extends Notification
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
            'toastType' => ToastType::SUCCESS->value,
            'message' => __('Groups generation done for tournament :tournament !', [
                'tournament' => $this->tournament->name,
            ]),
        ]);
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'translationKey' => __('Groups generation done for tournament :tournament !', locale: 'en'),
            'translationParams' => ['tournament' => $this->tournament->name],
        ];
    }
}
