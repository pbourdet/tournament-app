<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TournamentUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        readonly public Tournament $tournament,
        readonly public bool $shouldBroadcast = true,
        readonly public bool $shouldUpdateStatus = true,
    ) {
    }

    /** @return array<int, PrivateChannel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(sprintf('App.Models.Tournament.%s', $this->tournament->id)),
        ];
    }

    // Without this method, the event broadcast all public properties of the class
    /** @return array<string, string> */
    public function broadcastWith(): array
    {
        return [];
    }

    public function broadcastWhen(): bool
    {
        return $this->shouldBroadcast;
    }
}
