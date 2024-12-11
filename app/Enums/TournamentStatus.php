<?php

declare(strict_types=1);

namespace App\Enums;

enum TournamentStatus: string
{
    case WAITING_FOR_PLAYERS = 'Waiting for players';
    case SETUP_IN_PROGRESS = 'Setup in progress';
    case READY_TO_START = 'Ready to start';
    case IN_PROGRESS = 'In progress';
    case FINISHED = 'Finished';

    /** @var array<int, TournamentStatus> */
    public const array EDITABLE_STATUSES = [
        self::READY_TO_START,
        self::WAITING_FOR_PLAYERS,
        self::SETUP_IN_PROGRESS,
    ];
}
