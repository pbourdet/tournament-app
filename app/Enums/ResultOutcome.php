<?php

declare(strict_types=1);

namespace App\Enums;

enum ResultOutcome: string
{
    case WIN = 'Win';
    case LOSS = 'Loss';
    case TIE = 'Tie';
}
