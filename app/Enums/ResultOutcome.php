<?php

declare(strict_types=1);

namespace App\Enums;

enum ResultOutcome: string
{
    case WIN = 'win';
    case LOSS = 'loss';
    case TIE = 'tie';
}
