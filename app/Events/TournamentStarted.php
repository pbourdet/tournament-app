<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TournamentStarted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        readonly private Tournament $tournament,
    ) {
    }

    /** @return array<int, PrivateChannel> */
    public function broadcastOn(): array
    {
        $channels = $this->tournament->players->map(fn ($player): PrivateChannel => new PrivateChannel(sprintf('App.Models.User.%s', $player->id)))->all();

        if ($this->tournament->players->doesntContain($this->tournament->organizer)) {
            $channels[] = new PrivateChannel(sprintf('App.Models.User.%s', $this->tournament->organizer->id));
        }

        return $channels;
    }

    /** @return array<string, string> */
    public function broadcastWith(): array
    {
        return [
            'toastType' => 'success',
            'message' => __('The tournament :tournament has started. Good luck, have fun !', [
                'tournament' => $this->tournament->name,
            ]),
        ];
    }
}
